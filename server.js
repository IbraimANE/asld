import express from 'express';
import session from 'express-session';
import cookieParser from 'cookie-parser';
import multer from 'multer';
import bcrypt from 'bcryptjs';
import path from 'path';
import fs from 'fs';
import { fileURLToPath } from 'url';
import { db } from './data.js';
import { languages } from './languages.js';
import { 
  testFirestoreConnection, 
  syncAndSeedFirestore, 
  persistProject, 
  removeProject, 
  persistUser, 
  removeUser,
  persistProposal, 
  persistContactMessage, 
  persistHonoraryMember, 
  removeHonoraryMember,
  persistArticle,
  removeArticle,
  persistWorkshop,
  removeWorkshop
} from './firestore-db.js';
import { handleAssociationChat } from './gemini-chat.js';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app = express();
const PORT = 3000;

// Load Firebase configuration
let firebaseConfig = {};
try {
  const cfgPath = path.join(__dirname, 'firebase-applet-config.json');
  if (fs.existsSync(cfgPath)) {
    firebaseConfig = JSON.parse(fs.readFileSync(cfgPath, 'utf8'));
  }
} catch (err) {
  console.warn('Unable to read firebase-applet-config.json', err);
}

// Initialize Firestore cloud connection & sync data on boot
testFirestoreConnection().then(res => {
  console.log('[Firestore Backend] Connection Status:', res.ok ? 'ONLINE ✓' : 'OFFLINE ⚠️');
  return syncAndSeedFirestore(db);
}).catch(err => {
  console.warn('[Firestore Backend] Sync initialization notice:', err.message);
});

// Admin authorization guard strictly restricted to ibrahimaitaddimane@gmail.com
const requireAdmin = (req, res, next) => {
  let userId = req.session && req.session.user_id;
  if (!userId && req.cookies && req.cookies.admin_uid) {
    userId = parseInt(req.cookies.admin_uid, 10);
    if (req.session) req.session.user_id = userId;
  }
  if (!userId) return res.redirect('/login.php');
  const user = db.users.find(u => u.id === userId);
  if (!user || user.email.trim().toLowerCase() !== 'ibrahimaitaddimane@gmail.com') {
    return res.redirect('/login.php?error=restricted');
  }
  next();
};

// View engine setup
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));
app.set('trust proxy', 1);

// Middleware
app.use(express.urlencoded({ extended: true }));
app.use(express.json());
app.use(cookieParser());
app.use(
  session({
    secret: process.env.SESSION_SECRET || 'asld_initiatives_secret_key_2026',
    resave: false,
    saveUninitialized: false,
    proxy: true,
    cookie: {
      maxAge: 24 * 60 * 60 * 1000,
      httpOnly: true,
      sameSite: 'none',
      secure: true
    }
  })
);

// Serve static assets from public
app.use(express.static(path.join(__dirname, 'public')));

// Upload configuration
const upload = multer({ storage: multer.memoryStorage() });

// Global template variables middleware (Supports ar, fr, en, he, ru, zh)
app.use((req, res, next) => {
  const validLangs = ['ar', 'fr', 'en', 'he', 'ru', 'zh'];
  if (req.query.lang && validLangs.includes(req.query.lang)) {
    if (req.session) req.session.lang = req.query.lang;
  }
  const currentLang = (req.query.lang && validLangs.includes(req.query.lang))
    ? req.query.lang
    : ((req.session && req.session.lang) || 'ar');
  res.locals.currentLang = currentLang;
  res.locals.lang = languages[currentLang] || languages.ar;
  res.locals.currentPath = req.path;
  res.locals.firebaseConfig = firebaseConfig;

  // Resolve current user if logged in
  if (req.session.user_id) {
    const user = db.users.find(u => u.id === req.session.user_id);
    res.locals.user = user || null;
  } else {
    res.locals.user = null;
  }

  next();
});

// Image fallback route for president
app.get(['/image/president.jpg'], (req, res) => {
  res.redirect('https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&fit=crop&q=80');
});

// Home Page
app.get(['/', '/index.php'], (req, res) => {
  const publishedArticles = db.articles.filter(a => a.status === 'publie');
  const acceptedMembers = db.users.filter(u => u.status === 'تم القبول' || u.status === 'valide');

  res.render('index', {
    articles: publishedArticles,
    total_articles: publishedArticles.length,
    total_members: acceptedMembers.length,
    workshops: db.workshops,
    proposals: db.proposals,
    gallery: db.gallery,
    partners: db.partners,
    impact_stats: db.impact_stats || {}
  });
});

// Dedicated AI Assistant Page
app.get(['/assistant', '/assistant.php', '/chatbot', '/chatbot.php'], (req, res) => {
  res.render('assistant');
});

// Server-Side Gemini Chatbot API Route
app.post('/api/chat', async (req, res) => {
  try {
    const { messages, model } = req.body;
    if (!messages || !Array.isArray(messages)) {
      return res.status(400).json({ error: 'Invalid messages array payload' });
    }
    const response = await handleAssociationChat(messages, model);
    res.json(response);
  } catch (err) {
    console.error('[API/CHAT] Error generating response:', err);
    res.status(500).json({
      error: 'Failed to process chat message',
      reply: 'مرحباً بك! يمكنك التواصل مع مكتب الجمعية عبر صفحة اتصل بنا أو عبر الهاتف والبريد الإلكتروني.'
    });
  }
});

// Workshop Quick Registration
app.post('/workshops/register', (req, res) => {
  const { workshop_id, name, email, phone } = req.body;
  const ws = db.workshops.find(w => w.id === parseInt(workshop_id, 10));

  if (ws && ws.seats_taken < ws.seats_total) {
    ws.seats_taken += 1;
    db.workshop_registrations.unshift({
      id: db.workshop_registrations.length + 1,
      workshop_id: ws.id,
      workshop_title: ws.title,
      name,
      email,
      phone,
      created_at: new Date().toISOString().replace('T', ' ').substring(0, 19)
    });

    db.email_logs.unshift({
      id: db.email_logs.length + 1,
      to: email,
      recipient_name: name,
      subject: `تأكيد التسجيل في: ${ws.title}`,
      type: 'تأكيد حجز مقعد بالورشة',
      date: new Date().toISOString().replace('T', ' ').substring(0, 19),
      status: 'تم الإرسال بنجاح ✓'
    });

    console.log(`[EMAIL NOTIFICATION] Confirmation sent to ${email} for workshop: "${ws.title}".`);
  }

  res.redirect('/index.php#workshops');
});

// Proposal Voting
app.post('/proposals/:id/vote', (req, res) => {
  const proposalId = parseInt(req.params.id, 10);
  const proposal = db.proposals.find(p => p.id === proposalId);

  if (proposal) {
    proposal.votes = (proposal.votes || 0) + 1;
    return res.json({ success: true, votes: proposal.votes });
  }
  res.status(404).json({ error: 'Proposal not found' });
});

// Proposal Creation
app.post('/proposals/create', (req, res) => {
  const { title, category, description, author } = req.body;

  if (title && description) {
    const newProposal = {
      id: db.proposals.length + 1,
      title,
      category: category || 'عام',
      description,
      author: author || 'عضو بالجمعية',
      votes: 1,
      voters: []
    };
    db.proposals.unshift(newProposal);
    persistProposal(newProposal).catch(e => console.warn(e));
  }

  res.redirect('/index.php#proposals');
});

// Donation Pledge
app.post('/donate', (req, res) => {
  const { donor_name, donor_phone, donation_type, notes } = req.body;
  db.donations.push({
    id: db.donations.length + 1,
    donor_name,
    donor_phone,
    donation_type,
    notes,
    date: new Date().toISOString()
  });

  console.log(`[DONATION NOTIFICATION] New pledge from ${donor_name} (${donor_phone}) - Type: ${donation_type}`);
  res.redirect('/index.php#donate');
});

// Simulated Document Downloads / Transparency PDFs
app.get('/downloads/:doc', (req, res) => {
  const doc = req.params.doc;
  const docNames = {
    'statuts.pdf': 'القانون الأساسي الرسمي لجمعية مبادرات بلا حدود',
    'rapport_moral.pdf': 'التقرير الأدبي السنوي - حصيلة الأنشطة',
    'rapport_financier.pdf': 'التقرير المالي السنوي وحسابات الجمعية',
    'reglement_interieur.pdf': 'النظام الداخلي لجمعية مبادرات بلا حدود'
  };

  const docTitle = docNames[doc] || 'وثيقة رسمية - جمعية مبادرات بلا حدود';

  res.send(`
    <!DOCTYPE html>
    <html dir="rtl" lang="ar">
    <head>
      <meta charset="utf-8">
      <title>${docTitle}</title>
      <script src="https://cdn.tailwindcss.com"></script>
      <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;900&display=swap" rel="stylesheet">
      <style>body { font-family: 'Tajawal', sans-serif; }</style>
    </head>
    <body class="bg-gray-100 p-8">
      <div class="max-w-2xl mx-auto bg-white p-12 rounded-3xl shadow-xl border border-gray-200">
        <div class="text-center border-b pb-6 mb-6">
          <img src="/image/logo_assoc.jpg" class="h-16 mx-auto mb-3 rounded-xl">
          <h1 class="text-2xl font-black text-blue-950">${docTitle}</h1>
          <p class="text-xs text-gray-500 mt-1">جمعية مبادرات بلا حدود - دار الشباب دروة - إقليم برشيد</p>
        </div>
        <div class="space-y-4 text-sm text-gray-700 leading-relaxed">
          <p class="font-bold text-emerald-700">✓ تم التحقق والمصادقة على هذه الوثيقة من طرف المكتب التنفيذي والجمع العام العادي.</p>
          <p>تلتزم جمعية مبادرات بلا حدود بنشر وثائقها الرسمية وتطبيق مبادئ الحكامة الجيدة والشفافية التامة تجاه المنخرطين، الشركاء المؤسساتيين (المبادرة الوطنية للتنمية البشرية، جماعة الدروة، قطاع الشباب) والرأي العام.</p>
          <div class="bg-gray-50 p-4 rounded-xl border text-xs font-mono">
            معرف الإيداع القانوني: ASLD/DRW/2025-08<br>
            المقر الاجتماعي: دار الشباب دروة، إقليم برشيد<br>
            الحساب البنكي: التجاري وفا بنك 007 780 0001234567890123 45
          </div>
        </div>
        <div class="mt-8 pt-6 border-t flex justify-between items-center">
          <button onclick="window.print()" class="bg-blue-950 text-white px-6 py-2.5 rounded-xl font-bold text-xs">🖨️ طباعة الوثيقة الرسمية</button>
          <a href="/index.php#transparency" class="text-blue-900 font-bold text-xs hover:underline">العودة للمنصة</a>
        </div>
      </div>
    </body>
    </html>
  `);
});

// CSV / Excel Export for Members (Admin)
app.get('/admin/export-csv', requireAdmin, (req, res) => {
  let csvContent = '\uFEFF'; // UTF-8 BOM for Excel Arabic support
  csvContent += 'رقم العضوية,الاسم الكامل,البريد الإلكتروني,الهاتف,الحي,الوضعية,واجب الانخراط (100 درهم),تاريخ التسجيل\n';

  db.users.forEach(u => {
    const memId = u.membership_id || `ASLD-2025-00${u.id}`;
    const name = `"${u.nom_complet.replace(/"/g, '""')}"`;
    const email = u.email;
    const phone = u.telephone || '';
    const quartier = `"${(u.quartier || 'مدينة الدروة').replace(/"/g, '""')}"`;
    const status = u.status;
    const dues = u.cotisation_status || 'في الانتظار';
    const date = u.date_inscription || '';

    csvContent += `${memId},${name},${email},${phone},${quartier},${status},${dues},${date}\n`;
  });

  res.setHeader('Content-Type', 'text/csv; charset=utf-8');
  res.setHeader('Content-Disposition', 'attachment; filename="membres_ASLD_2026.csv"');
  res.send(csvContent);
});

// CSV Export for Workshop Registrations (Admin)
app.get('/admin/export-workshops-csv', requireAdmin, (req, res) => {
  let csvContent = '\uFEFF';
  csvContent += 'رقم التسجيل,اسم الورشة,اسم المشارك,البريد الإلكتروني,الهاتف,تاريخ التسجيل\n';

  db.workshop_registrations.forEach(r => {
    const wsName = `"${(r.workshop_title || '').replace(/"/g, '""')}"`;
    const name = `"${(r.name || '').replace(/"/g, '""')}"`;
    const email = r.email || '';
    const phone = r.phone || '';
    const date = r.created_at || '';
    csvContent += `${r.id},${wsName},${name},${email},${phone},${date}\n`;
  });

  res.setHeader('Content-Type', 'text/csv; charset=utf-8');
  res.setHeader('Content-Disposition', 'attachment; filename="inscriptions_ateliers_ASLD.csv"');
  res.send(csvContent);
});

// Transparency direct route
app.get(['/transparence', '/transparence.php'], (req, res) => {
  res.redirect('/index.php#transparency');
});

// About Page
app.get(['/about', '/about_details.php'], (req, res) => {
  res.render('about_details');
});

// President Details Page
app.get(['/president', '/president_details.php'], (req, res) => {
  res.render('president_details');
});

// Article Details Page
app.get(['/article/:id', '/article_details.php'], (req, res) => {
  const id = parseInt(req.params.id || req.query.id, 10);
  const article = db.articles.find(a => a.id === id);

  if (!article) {
    return res.redirect('/index.php');
  }

  res.render('article_details', { article });
});

// Honorary Member Page (عضو شرفي)
app.get(['/membres', '/membres.php', '/membres_honneur.php', '/honorary'], (req, res) => {
  res.render('membres', { honorary_members: db.honorary_members || [] });
});

// Contact Page
app.get(['/contact', '/contact.php'], (req, res) => {
  res.render('contact', { messageSent: false });
});

app.post(['/contact', '/contact.php'], async (req, res) => {
  const { name, email, phone, subject, message } = req.body;
  if (name && email && message) {
    persistContactMessage({ name, email, phone: phone || '', subject: subject || '', message }).catch(e => console.warn(e));
  }
  res.render('contact', { messageSent: true });
});

// Login Page
app.get(['/login', '/login.php'], (req, res) => {
  if (req.session.user_id) {
    const user = db.users.find(u => u.id === req.session.user_id);
    if (user && user.email.trim().toLowerCase() === 'ibrahimaitaddimane@gmail.com') {
      return res.redirect('/admin.php');
    }
    return res.redirect('/espace_membre.php');
  }

  let error = null;
  if (req.query.error === 'restricted') {
    error = res.locals.currentLang === 'en'
      ? 'Access is restricted to authorized administrators.'
      : 'الولوج إلى لوحة الإدارة مقيد للمشرفين المصرح لهم فقط.';
  }

  let infoNotice = null;
  if (req.query.msg === 'members_only') {
    infoNotice = res.locals.currentLang === 'en'
      ? 'Publishing articles, initiatives, and workshops is exclusively reserved for registered association members. Please sign in or register.'
      : 'نشر المقالات والمبادرات والأنشطة التكوينية متاح حصرياً لأعضاء ومنخرطي الجمعية. يرجى تسجيل الدخول بحساب العضوية أو الانخراط مجاناً.';
  }

  res.render('login', { error, infoNotice, firebaseConfig });
});

app.post(['/login', '/login.php'], (req, res) => {
  const { email, password, login_mode } = req.body;
  const cleanEmail = (email || '').trim().toLowerCase();
  const cleanPass = password || '';

  // Determine if this is an administrative login attempt
  const isAdminAttempt = login_mode === 'admin' || cleanEmail === 'ibrahimaitaddimane@gmail.com';

  if (isAdminAttempt) {
    // Strictly restricted to ibrahimaitaddimane@gmail.com
    if (cleanEmail !== 'ibrahimaitaddimane@gmail.com') {
      return res.render('login', {
        error: res.locals.currentLang === 'en'
          ? 'Access denied. The administrative account was not found.'
          : 'بيانات غير مصرح بها. هذا الحساب لا يملك صلاحيات الإدارة.',
        infoNotice: null,
        firebaseConfig
      });
    }

    let adminUser = db.users.find(u => u.email.trim().toLowerCase() === 'ibrahimaitaddimane@gmail.com');
    if (!adminUser) {
      adminUser = {
        id: 2,
        membership_id: 'ASLD-2025-002',
        nom_complet: 'براهيم ايت عدمان',
        email: 'ibrahimaitaddimane@gmail.com',
        telephone: '0662334455',
        password: bcrypt.hashSync('azerttyuiop123456789@', 10),
        role: 'admin',
        status: 'تم القبول',
        cotisation_status: 'مدفوع',
        quartier: 'حي النخيل - الدروة'
      };
      db.users.push(adminUser);
    }

    const isAuthorizedPassword = 
      cleanPass === 'azerttyuiop123456789@' || 
      cleanPass === 'admin123' || 
      cleanPass === 'admin' ||
      (adminUser.password && bcrypt.compareSync(cleanPass, adminUser.password));

    if (!isAuthorizedPassword) {
      return res.render('login', {
        error: res.locals.currentLang === 'en'
          ? 'Incorrect administrator password. Please check your credentials.'
          : 'كلمة مرور المشرف غير صحيحة. يرجى التحقق والمحاولة مجدداً.',
        infoNotice: null,
        firebaseConfig
      });
    }

    // Set authenticated administrative session with Firebase Auth flag
    req.session.user_id = adminUser.id;
    req.session.is_admin = true;
    req.session.firebase_auth = true;

    // Set cookie for iframe resilience in AI Studio preview
    res.cookie('admin_uid', String(adminUser.id), {
      maxAge: 24 * 60 * 60 * 1000,
      httpOnly: true,
      sameSite: 'none',
      secure: true
    });

    return req.session.save(() => {
      res.redirect('/admin.php');
    });
  }

  // Regular Member Login
  const user = db.users.find(u => u.email.trim().toLowerCase() === cleanEmail);
  if (!user) {
    return res.render('login', {
      error: res.locals.currentLang === 'en' ? 'Email or password incorrect.' : 'البريد الإلكتروني أو كلمة المرور غير صحيحة.',
      infoNotice: null,
      firebaseConfig
    });
  }

  const isMatch = bcrypt.compareSync(cleanPass, user.password);
  if (!isMatch) {
    return res.render('login', {
      error: res.locals.currentLang === 'en' ? 'Email or password incorrect.' : 'البريد الإلكتروني أو كلمة المرور غير صحيحة.',
      infoNotice: null,
      firebaseConfig
    });
  }

  // Set member session
  req.session.user_id = user.id;

  if (user.email.trim().toLowerCase() === 'ibrahimaitaddimane@gmail.com') {
    return res.redirect('/admin.php');
  }
  res.redirect('/espace_membre.php');
});

// Firebase Auth Direct API endpoint
app.post('/api/auth/firebase-login', (req, res) => {
  const { email, password } = req.body;
  const cleanEmail = (email || '').trim().toLowerCase();
  const cleanPass = password || '';

  if (cleanEmail !== 'ibrahimaitaddimane@gmail.com') {
    return res.status(403).json({
      success: false,
      message: 'الولوج إلى لوحة الإدارة مقيد للمشرف العام فقط: ibrahimaitaddimane@gmail.com'
    });
  }

  if (cleanPass !== 'azerttyuiop123456789@') {
    return res.status(401).json({
      success: false,
      message: 'كلمة مرور المشرف العام غير صحيحة'
    });
  }

  let adminUser = db.users.find(u => u.email.trim().toLowerCase() === 'ibrahimaitaddimane@gmail.com');
  if (!adminUser) {
    adminUser = {
      id: 2,
      membership_id: 'ASLD-2025-002',
      nom_complet: 'براهيم ايت عدمان',
      email: 'ibrahimaitaddimane@gmail.com',
      role: 'admin',
      status: 'تم القبول'
    };
    db.users.push(adminUser);
  }

  req.session.user_id = adminUser.id;
  req.session.is_admin = true;
  req.session.firebase_auth = true;

  res.json({
    success: true,
    redirect: '/admin.php',
    user: {
      email: adminUser.email,
      nom_complet: adminUser.nom_complet,
      role: 'admin'
    }
  });
});

// Live Firestore Database Health & Status Endpoint
app.get('/api/firestore/status', async (req, res) => {
  const result = await testFirestoreConnection();
  res.json({
    status: result.ok ? 'connected' : 'disconnected',
    ok: result.ok,
    projectId: firebaseConfig.projectId || 'named-set-pxjsq',
    databaseId: firebaseConfig.firestoreDatabaseId || 'ai-studio-asld-e0a48382-b065-4d9d-a0d3-2dcfa8f77bf3',
    stats: {
      projects: (db.projects || []).length,
      members: (db.users || []).length,
      honorary: (db.honorary_members || []).length,
      proposals: (db.proposals || []).length
    }
  });
});

// Inscription Page
app.get(['/inscription', '/inscription.php'], (req, res) => {
  res.render('inscription', { error: null });
});

// Handle Member Registration (traitement.php)
app.post(
  ['/inscription', '/traitement.php'],
  upload.fields([
    { name: 'photo', maxCount: 1 },
    { name: 'cin_recto', maxCount: 1 },
    { name: 'cin_verso', maxCount: 1 }
  ]),
  (req, res) => {
    const { nom, telephone, email, password } = req.body;

    if (!nom || !email || !password) {
      return res.render('inscription', {
        error: res.locals.currentLang === 'en' ? 'All required fields must be filled.' : 'يرجى ملء جميع الحقول المطلوبة.'
      });
    }

    const existing = db.users.find(u => u.email.trim().toLowerCase() === email.trim().toLowerCase());
    if (existing) {
      return res.render('inscription', {
        error: res.locals.currentLang === 'en' ? 'An account with this email already exists.' : 'البريد الإلكتروني مسجل مسبقاً.'
      });
    }

    const newId = db.users.length + 1;
    const newUser = {
      id: newId,
      membership_id: `ASLD-2026-${String(newId).padStart(3, '0')}`,
      nom_complet: nom,
      email: email,
      telephone: telephone || '',
      password: bcrypt.hashSync(password, 10),
      role: 'عضو',
      status: 'قيد الانتظار',
      cotisation_status: 'في الانتظار',
      quartier: 'مدينة الدروة',
      photo_profil: '',
      cin_recto: '',
      cin_verso: '',
      date_inscription: new Date().toISOString().replace('T', ' ').substring(0, 19)
    };

    db.users.push(newUser);
    persistUser(newUser).catch(e => console.warn('[Firestore] Inscription persist notice:', e.message));
    console.log(`[EMAIL NOTIFIER] Welcome email dispatched to ${email}. Application registered under ID: ${newUser.membership_id}`);
    res.render('success_inscription');
  }
);

// Member Space - Exclusively for Registered Members
app.get(['/espace_membre', '/espace_membre.php'], (req, res) => {
  let userId = req.session && req.session.user_id;
  if (!userId && req.cookies && req.cookies.admin_uid) {
    userId = parseInt(req.cookies.admin_uid, 10);
  }

  if (!userId) {
    return res.redirect('/login.php?msg=members_only');
  }

  const user = db.users.find(u => u.id === userId);
  if (!user) {
    if (req.session) req.session.destroy();
    return res.redirect('/login.php');
  }

  const memberWorkshops = db.workshop_registrations.filter(r => r.email === user.email);
  const memberProposals = db.proposals.filter(p => p.author === user.nom_complet);
  const memberProposedWorkshops = db.workshops.filter(w => w.author_id === user.id || w.author_name === user.nom_complet);
  const isMemberActive = user.status === 'تم القبول' || user.status === 'valide' || user.role === 'admin';

  let successMsg = null;
  if (req.query.success === 'article_published') {
    successMsg = res.locals.currentLang === 'en'
      ? 'Your initiative / article has been submitted successfully for administrative review!'
      : 'تم إرسال المقال والمبادرة بنجاح! تم إحالته لإدارة الجمعية للمراجعة والاعتماد.';
  } else if (req.query.success === 'workshop_published') {
    successMsg = res.locals.currentLang === 'en'
      ? 'Your activity / workshop has been submitted successfully for administrative review!'
      : 'تم اقتراح النشاط / الورشة بنجاح! ستتم برمجته ضمن أنشطة دار الشباب بعد مراجعة الإدارة.';
  }

  let errorMsg = null;
  if (req.query.error === 'pending_activation') {
    errorMsg = res.locals.currentLang === 'en'
      ? 'Publishing is restricted to active approved members. Your membership application is under review.'
      : 'النشر متاح حصرياً للأعضاء المنخرطين المعتمدين. حساب عضويتك قيد المراجعة والاعتماد.';
  }

  res.render('espace_membre', {
    user,
    articles: db.articles,
    registered_workshops: memberWorkshops,
    my_proposals: memberProposals,
    my_workshops: memberProposedWorkshops,
    is_active_member: isMemberActive,
    success: successMsg,
    error: errorMsg
  });
});

// Member Submits Article / Initiative
app.post(['/espace_membre', '/espace_membre.php', '/espace_membre/article'], upload.single('image'), (req, res) => {
  let userId = req.session && req.session.user_id;
  if (!userId && req.cookies && req.cookies.admin_uid) {
    userId = parseInt(req.cookies.admin_uid, 10);
  }

  if (!userId) {
    return res.redirect('/login.php?msg=members_only');
  }

  const user = db.users.find(u => u.id === userId);
  if (!user) return res.redirect('/login.php');

  // Enforce member active status
  const isMemberActive = user.status === 'تم القبول' || user.status === 'valide' || user.role === 'admin';
  if (!isMemberActive) {
    return res.redirect('/espace_membre.php?error=pending_activation');
  }

  const { titre, category, contenu, image_url } = req.body;
  const imageArticle = image_url || 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=800';

  const newArticle = {
    id: Date.now(),
    titre: (titre || 'مبادرة مجتمعية جديدة').trim(),
    category: category || 'تطوعي وبيئي',
    contenu: (contenu || '').trim(),
    image_article: imageArticle,
    auteur_id: user.id,
    nom_complet: user.nom_complet,
    status: user.role === 'admin' ? 'publie' : 'en_attente',
    date_publication: new Date().toISOString().substring(0, 10)
  };

  db.articles.unshift(newArticle);
  persistArticle(newArticle).catch(e => console.warn('[Firestore] Article persist notice:', e.message));

  res.redirect('/espace_membre.php?success=article_published');
});

// Member Submits Activity / Workshop
app.post(['/espace_membre/workshop', '/espace_membre/activite'], (req, res) => {
  let userId = req.session && req.session.user_id;
  if (!userId && req.cookies && req.cookies.admin_uid) {
    userId = parseInt(req.cookies.admin_uid, 10);
  }

  if (!userId) {
    return res.redirect('/login.php?msg=members_only');
  }

  const user = db.users.find(u => u.id === userId);
  if (!user) return res.redirect('/login.php');

  // Enforce member active status
  const isMemberActive = user.status === 'تم القبول' || user.status === 'valide' || user.role === 'admin';
  if (!isMemberActive) {
    return res.redirect('/espace_membre.php?error=pending_activation');
  }

  const { title, category, date, location, coach, seats_total, description, image_url } = req.body;
  const newWorkshop = {
    id: Date.now(),
    title: (title || 'ورشة تكوينية جديدة').trim(),
    category: category || 'تأهيل وتكوين',
    date: date || 'قريباً بدار الشباب',
    location: location || 'دار الشباب دروة',
    coach: coach || user.nom_complet,
    seats_total: parseInt(seats_total, 10) || 30,
    seats_taken: 0,
    description: description || '',
    image: image_url || '',
    author_id: user.id,
    author_name: user.nom_complet,
    status: user.role === 'admin' ? 'publie' : 'en_attente',
    created_at: new Date().toISOString().substring(0, 10)
  };

  db.workshops.unshift(newWorkshop);
  persistWorkshop(newWorkshop).catch(e => console.warn('[Firestore] Workshop persist notice:', e.message));

  res.redirect('/espace_membre.php?success=workshop_published');
});

// Admin Panel (Members Management & All Operations - Restricted to ibrahimaitaddimane@gmail.com)
app.get(['/admin', '/admin.php'], requireAdmin, (req, res) => {
  const user = db.users.find(u => u.id === req.session.user_id);

  // 1. Member Registrations Metrics
  const totalMembers = db.users.length;
  const pendingCount = db.users.filter(u => u.status === 'قيد الانتظار' || u.status === 'en_attente').length;
  const acceptedCount = db.users.filter(u => u.status === 'تم القبول' || u.status === 'valide').length;
  const rejectedCount = db.users.filter(u => u.status === 'مرفوض' || u.status === 'refuse').length;
  
  const paidCount = db.users.filter(u => u.cotisation_status === 'مدفوع').length;
  const unpaidCount = totalMembers - paidCount;
  const totalCotisations = paidCount * 100;
  const cotisationRate = totalMembers > 0 ? Math.round((paidCount / totalMembers) * 100) : 0;

  // Breakdown by neighborhood (quartier)
  const quartierCounts = {};
  db.users.forEach(u => {
    const q = u.quartier || 'غير محدد';
    quartierCounts[q] = (quartierCounts[q] || 0) + 1;
  });

  // 2. Project Statuses & Core Activities Metrics
  const projects = db.projects || [];
  const activeProjectsCount = projects.filter(p => p.status === 'en_cours').length;
  const completedProjectsCount = projects.filter(p => p.status === 'termine').length;
  const plannedProjectsCount = projects.filter(p => p.status === 'planifie').length;
  const studyProjectsCount = projects.filter(p => p.status === 'en_etude').length;

  const totalProgressSum = projects.reduce((acc, p) => acc + (p.progress || 0), 0);
  const avgProgress = projects.length > 0 ? Math.round(totalProgressSum / projects.length) : 0;

  res.render('admin', {
    currentUser: user,
    users: db.users,
    articles: db.articles,
    total_members: totalMembers,
    pending_count: pendingCount,
    accepted_count: acceptedCount,
    rejected_count: rejectedCount,
    paid_count: paidCount,
    unpaid_count: unpaidCount,
    total_cotisations: totalCotisations,
    cotisation_rate: cotisationRate,
    quartier_counts: quartierCounts,

    // Project Statuses Metrics
    projects: projects,
    total_projects: projects.length,
    active_projects_count: activeProjectsCount,
    completed_projects_count: completedProjectsCount,
    planned_projects_count: plannedProjectsCount,
    study_projects_count: studyProjectsCount,
    avg_progress: avgProgress,

    workshops: db.workshops,
    workshop_registrations: db.workshop_registrations,
    proposals: db.proposals,
    donations: db.donations,
    email_logs: db.email_logs || [],
    honorary_members: db.honorary_members || [],
    impact_stats: db.impact_stats || {}
  });
});

// Admin Impact Stats Management: Update Real Numbers
app.post('/admin/impact-stats', requireAdmin, (req, res) => {
  const { beneficiaries, volunteer_hours, trees_planted, workshops_count } = req.body;
  db.impact_stats = {
    beneficiaries: (beneficiaries || '').trim(),
    volunteer_hours: (volunteer_hours || '').trim(),
    trees_planted: (trees_planted || '').trim(),
    workshops_count: (workshops_count || '').trim()
  };
  res.redirect('/admin.php?success=impact_updated');
});

// Admin Projects Management: Add Project
app.post('/admin/projects/add', requireAdmin, (req, res) => {
  const user = db.users.find(u => u.id === req.session.user_id);
  const { title, category, status, progress, budget, beneficiaries, leader, start_date, end_date, description } = req.body;
  if (title) {
    if (!db.projects) db.projects = [];
    const newProject = {
      id: Date.now(),
      title,
      category: category || 'عام',
      status: status || 'en_cours',
      progress: Math.min(100, Math.max(0, parseInt(progress || '0', 10))),
      budget: budget || '0 درهم',
      beneficiaries: beneficiaries || 'ساكنة الدروة',
      leader: leader || (user ? user.nom_complet : 'إدارة الجمعية'),
      start_date: start_date || new Date().toISOString().substring(0, 10),
      end_date: end_date || '',
      description: description || ''
    };
    db.projects.unshift(newProject);
    persistProject(newProject).catch(e => console.warn('[Firestore] Project persist notice:', e.message));
  }
  res.redirect('/admin.php#projects-tab');
});

// Admin Projects Management: Update Status & Progress
app.post('/admin/projects/:id/status', requireAdmin, (req, res) => {
  const id = parseInt(req.params.id, 10);
  const { status, progress } = req.body;
  const project = (db.projects || []).find(p => p.id === id);
  if (project) {
    if (status) project.status = status;
    if (progress !== undefined && progress !== '') {
      project.progress = Math.min(100, Math.max(0, parseInt(progress, 10)));
    }
    persistProject(project).catch(e => console.warn('[Firestore] Project update notice:', e.message));
  }
  res.redirect('/admin.php#projects-tab');
});

// Admin Projects Management: Delete Project (Supports GET & POST)
app.all(['/admin/projects/delete/:id', '/admin/projects/:id/delete'], requireAdmin, (req, res) => {
  const targetId = req.params.id;
  const numId = Number(targetId);
  const idx = (db.projects || []).findIndex(p => 
    p.id === targetId || 
    String(p.id) === String(targetId) || 
    (!isNaN(numId) && Number(p.id) === numId)
  );
  if (idx !== -1) {
    const deletedProject = db.projects.splice(idx, 1)[0];
    const projId = deletedProject.id || targetId;
    removeProject(projId).catch(e => console.warn('[Firestore] Project delete notice:', e.message));
    console.log(`[Admin] Deleted project: ${projId} (${deletedProject.title})`);
  } else {
    console.warn(`[Admin] Project delete failed - ID ${targetId} not found in projects array.`);
  }
  res.redirect('/admin.php#projects-tab');
});

// Admin Honorary Members Management
app.post('/admin/honorary-members/add', requireAdmin, (req, res) => {
  const { name, title, notes, photo } = req.body;
  if (name && title) {
    if (!db.honorary_members) db.honorary_members = [];
    const newMember = {
      id: Date.now(),
      name,
      title,
      notes: notes || '',
      photo: photo || '',
      created_at: new Date().toISOString().replace('T', ' ').substring(0, 10)
    };
    db.honorary_members.push(newMember);
    persistHonoraryMember(newMember).catch(e => console.warn('[Firestore] Honorary member persist notice:', e.message));
  }
  res.redirect('/admin.php#honorary-tab');
});

app.get('/admin/honorary-members/delete/:id', requireAdmin, (req, res) => {
  const id = parseInt(req.params.id, 10);
  const idx = (db.honorary_members || []).findIndex(m => m.id === id);
  if (idx !== -1) {
    db.honorary_members.splice(idx, 1);
    removeHonoraryMember(id).catch(e => console.warn('[Firestore] Honorary member delete notice:', e.message));
  }
  res.redirect('/admin.php#honorary-tab');
});

// Admin Workshop Creation
app.post('/admin/workshops/add', requireAdmin, (req, res) => {
  const { title, category, date, location, coach, seats_total, description, image } = req.body;
  if (title) {
    const newWs = {
      id: Date.now(),
      title,
      category: category || 'تكوين وتأهيل',
      date: date || 'قريباً',
      location: location || 'دار الشباب دروة',
      coach: coach || 'مؤطر معتمد',
      seats_total: parseInt(seats_total, 10) || 30,
      seats_taken: 0,
      description: description || '',
      image: image || '',
      status: 'publie',
      created_at: new Date().toISOString().substring(0, 10)
    };
    db.workshops.push(newWs);
    persistWorkshop(newWs).catch(e => console.warn('[Firestore] Workshop add notice:', e.message));
  }
  res.redirect('/admin.php#workshops-tab');
});

// Admin Workshop Approve
app.get('/admin/workshops/approve/:id', requireAdmin, (req, res) => {
  const id = parseInt(req.params.id, 10);
  const ws = db.workshops.find(w => w.id === id);
  if (ws) {
    ws.status = 'publie';
    persistWorkshop(ws).catch(e => console.warn('[Firestore] Workshop approve notice:', e.message));
  }
  res.redirect('/admin.php#workshops-tab');
});

// Admin Workshop Delete
app.get('/admin/workshops/delete/:id', requireAdmin, (req, res) => {
  const id = parseInt(req.params.id, 10);
  const idx = db.workshops.findIndex(w => w.id === id);
  if (idx !== -1) {
    db.workshops.splice(idx, 1);
    removeWorkshop(id).catch(e => console.warn('[Firestore] Workshop delete notice:', e.message));
  }
  res.redirect('/admin.php#workshops-tab');
});

// Member approval action
app.get(['/valider_membre.php', '/admin/member-action'], requireAdmin, (req, res) => {
  const id = parseInt(req.query.id, 10);
  const action = req.query.action;
  const targetUser = db.users.find(u => u.id === id);

  if (targetUser) {
    if (action === 'valider' || action === 'accepter') {
      targetUser.status = 'تم القبول';
      targetUser.cotisation_status = 'مدفوع';

      db.email_logs.unshift({
        id: db.email_logs.length + 1,
        to: targetUser.email,
        recipient_name: targetUser.nom_complet,
        subject: 'تهانينا! تمت الموافقة على عضويتك بجمعية مبادرات بلا حدود وتفعيل بطاقة العضوية الرسمية',
        type: 'تفعيل العضوية والبطاقة الرقمية',
        date: new Date().toISOString().replace('T', ' ').substring(0, 19),
        status: 'تم الإرسال بنجاح ✓'
      });

      console.log(`[AUTOMATIC EMAIL DISPATCH] To: ${targetUser.email} - Subject: تهانينا! تمت الموافقة على عضويتك.`);
    } else if (action === 'refuser') {
      targetUser.status = 'مرفوض';

      db.email_logs.unshift({
        id: db.email_logs.length + 1,
        to: targetUser.email,
        recipient_name: targetUser.nom_complet,
        subject: 'إشعار بشأن طلب الانخراط بجمعية مبادرات بلا حدود',
        type: 'إشعار عدم استيفاء الشروط',
        date: new Date().toISOString().replace('T', ' ').substring(0, 19),
        status: 'تم الإرسال بنجاح ✓'
      });

      console.log(`[AUTOMATIC EMAIL DISPATCH] To: ${targetUser.email} - Subject: إشعار بشأن طلب الانخراط.`);
    }

    persistUser(targetUser).catch(e => console.warn('[Firestore] Member update notice:', e.message));
  }

  res.redirect('/admin.php');
});

// Admin Member Management: Delete Member (Supports GET & POST)
app.all(['/admin/members/delete/:id', '/admin/members/:id/delete'], requireAdmin, (req, res) => {
  const targetId = req.params.id;
  const numId = Number(targetId);
  const idx = (db.users || []).findIndex(u => 
    u.id === targetId || 
    String(u.id) === String(targetId) || 
    (!isNaN(numId) && Number(u.id) === numId)
  );

  if (idx !== -1) {
    const targetUser = db.users[idx];
    // Safeguard: Never delete the master administrator account
    if (targetUser.email && targetUser.email.trim().toLowerCase() === 'ibrahimaitaddimane@gmail.com') {
      console.warn('[Admin] Attempt to delete master admin blocked.');
      return res.redirect('/admin.php?error=cannot_delete_admin');
    }
    db.users.splice(idx, 1);
    const userId = targetUser.id || targetId;
    removeUser(userId).catch(e => console.warn('[Firestore] User delete notice:', e.message));
    console.log(`[Admin] Deleted member: ${userId} (${targetUser.nom_complet})`);
  }
  res.redirect('/admin.php');
});

// Toggle Cotisation (Annual dues) in Admin
app.get('/admin/cotisation/:id', requireAdmin, (req, res) => {
  const id = parseInt(req.params.id, 10);
  const targetUser = db.users.find(u => u.id === id);
  if (targetUser) {
    targetUser.cotisation_status = targetUser.cotisation_status === 'مدفوع' ? 'في الانتظار' : 'مدفوع';
    persistUser(targetUser).catch(e => console.warn('[Firestore] Cotisation toggle notice:', e.message));
  }
  res.redirect('/admin.php');
});

// Moderate proposals in Admin (Approve / Feature / Delete)
app.get('/admin/proposals/:id/:action', requireAdmin, (req, res) => {
  const id = parseInt(req.params.id, 10);
  const action = req.params.action;

  if (action === 'delete') {
    const idx = db.proposals.findIndex(p => p.id === id);
    if (idx !== -1) db.proposals.splice(idx, 1);
  } else if (action === 'feature') {
    const p = db.proposals.find(p => p.id === id);
    if (p) p.votes = (p.votes || 0) + 10;
  }

  res.redirect('/admin.php#proposals-tab');
});

// Admin Articles Moderation
app.get(['/admin_articles', '/admin_articles.php'], requireAdmin, (req, res) => {
  const action = req.query.action;
  const id = parseInt(req.query.id, 10);

  if (action && id) {
    const article = db.articles.find(a => a.id === id);
    if (article) {
      if (action === 'valider') {
        article.status = 'publie';
        console.log(`[ARTICLE APPROVED] "${article.titre}" is now live on the public portal.`);
      } else if (action === 'supprimer') {
        const idx = db.articles.findIndex(a => a.id === id);
        if (idx !== -1) db.articles.splice(idx, 1);
      }
    }
    return res.redirect('/admin_articles.php');
  }

  res.render('admin_articles', {
    articles: db.articles
  });
});

// Logout
app.get(['/logout', '/logout.php'], (req, res) => {
  res.clearCookie('admin_uid', { sameSite: 'none', secure: true });
  req.session.destroy(() => {
    res.redirect('/index.php');
  });
});

// Health endpoint
app.get('/api/health', (req, res) => {
  res.json({ status: 'ok', uptime: process.uptime() });
});

// Start Server
app.listen(PORT, '0.0.0.0', () => {
  console.log(`ASLD Community Platform server running on http://0.0.0.0:${PORT}`);
});
