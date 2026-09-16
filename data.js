import bcrypt from 'bcryptjs';

const passwordAdmin = bcrypt.hashSync('admin123', 10);
const passwordMember = bcrypt.hashSync('member123', 10);
const passwordIbrahim = bcrypt.hashSync('azerttyuiop123456789@', 10);

export const db = {
  users: [
    {
      id: 1,
      membership_id: 'ASLD-2025-001',
      nom_complet: 'الخدير الغرابي (رئيس الجمعية)',
      email: 'admin@assoc.ma',
      telephone: '0661122334',
      password: passwordAdmin,
      role: 'president',
      status: 'تم القبول',
      cotisation_status: 'مدفوع',
      quartier: 'وسط المدينة - الدروة',
      photo_profil: 'president.jpg',
      cin_recto: '',
      cin_verso: '',
      date_inscription: '2025-01-01 10:00:00'
    },
    {
      id: 2,
      membership_id: 'ASLD-2025-002',
      nom_complet: 'إبراهيم أيت الديمان (مدير ومسؤول المنصة)',
      email: 'ibrahimaitaddimane@gmail.com',
      telephone: '0662334455',
      password: passwordIbrahim,
      role: 'admin',
      status: 'تم القبول',
      cotisation_status: 'مدفوع',
      quartier: 'حي النخيل - الدروة',
      photo_profil: '',
      cin_recto: '',
      cin_verso: '',
      date_inscription: '2025-01-05 11:30:00'
    },
    {
      id: 3,
      membership_id: 'ASLD-2025-003',
      nom_complet: 'كريم البودالي (مسؤول اللوجستيك والتطوع)',
      email: 'responsable@assoc.ma',
      telephone: '0665778899',
      password: passwordAdmin,
      role: 'responsable',
      status: 'تم القبول',
      cotisation_status: 'مدفوع',
      quartier: 'حي الوفاء - الدروة',
      photo_profil: '',
      cin_recto: '',
      cin_verso: '',
      date_inscription: '2025-01-10 14:00:00'
    }
  ],

  // Community Projects & Strategic Initiatives of the Association (مشاريع وأنشطة الجمعية)
  projects: [
    {
      id: 1,
      title: 'القافلة الطبية المجانية متعددة التخصصات (دورة 2026)',
      category: 'صحي وتضامني',
      status: 'en_cours', // en_cours, termine, planifie, en_etude
      progress: 65,
      budget: '35,000 درهم',
      beneficiaries: '450 مستفيد',
      leader: 'لجنة العمل الصحي والتضامني',
      start_date: '2026-02-15',
      end_date: '2026-04-10',
      description: 'تنظيم حملة فحوصات مجانية لطب العيون، السكري، والضغط الدموي لفائدة ساكنة الأحياء الهشة بالدروة.'
    },
    {
      id: 2,
      title: 'مبادرة الدروة الخضراء: تشجير محيط المؤسسات التعليمية',
      category: 'بيئي وتطوعي',
      status: 'termine',
      progress: 100,
      budget: '12,500 درهم',
      beneficiaries: '3 مدارس عمومية',
      leader: 'نادي البيئة والمواطنة',
      start_date: '2026-01-10',
      end_date: '2026-02-28',
      description: 'غرس 200 شجرة وتهيئة المساحات الخضراء بثلاث مدارس بالدروة بالتعاون مع التلاميذ والشباب المتطوعين.'
    },
    {
      id: 3,
      title: 'حاضنة التمكين الرقمي لشباب الدروة بدار الشباب',
      category: 'تكوين وتأهيل',
      status: 'en_cours',
      progress: 40,
      budget: '22,000 درهم',
      beneficiaries: '80 شاب وشابة',
      leader: 'قطب الابتكار والرقمنة',
      start_date: '2026-03-01',
      end_date: '2026-06-30',
      description: 'سلسلة ورشات أسبوعية مجانية في البرمجة وتصميم الجرافيك والتسويق الإلكتروني لتيسير الإدماج المهني.'
    },
    {
      id: 4,
      title: 'دوري رمضان لكرة القدم المصغرة للناشئين والشباب',
      category: 'رياضي وتربوي',
      status: 'planifie',
      progress: 15,
      budget: '18,000 درهم',
      beneficiaries: '16 فريق محلي',
      leader: 'اللجنة الرياضية والشبابية',
      start_date: '2026-03-20',
      end_date: '2026-04-18',
      description: 'دوري رمضاني سنوي بملاعب القرب لتشجيع الروح الرياضية ومكافحة الانحراف لدى فئات اليافعين والشباب.'
    },
    {
      id: 5,
      title: 'مركز الاستماع والتوجيه الأسري ومحو الأمية الوظيفية',
      category: 'اجتماعي وحقوقي',
      status: 'en_etude',
      progress: 0,
      budget: '40,000 درهم',
      beneficiaries: '120 امرأة وربة بيت',
      leader: 'مكتب الجمعية والشؤون الاجتماعية',
      start_date: '2026-05-01',
      end_date: '2026-11-30',
      description: 'مشروع شراكة مع مؤسسات المبادرة الوطنية للتنمية البشرية لإحداث فضاء دعم واستماع وتأهيل للنساء بالدروة.'
    }
  ],

  // Left empty to be filled with real content
  articles: [],

  // Left empty to be filled in the future
  workshops: [],

  // Left empty to be filled in the future
  workshop_registrations: [],

  // Left empty to be filled by community proposals
  proposals: [],

  // Fضاء العضو الشرفي - فارغ لكي يتم ملؤه مستقبلاً
  honorary_members: [],

  // معرض الصور والوسائط - فارغ من أي أمثلة تمهيداً لتوثيق أنشطة الجمعية الحقيقية
  gallery: [],

  // Left empty to be filled by real support requests
  donations: [],

  // Left empty - will log actual dispatches
  email_logs: [],

  partners: [
    {
      name: 'دار الشباب دروة',
      role: 'شريك المقر والإشعاع الثقافي',
      icon: '🏛️'
    },
    {
      name: 'المبادرة الوطنية للتنمية البشرية (INDH)',
      role: 'شريك التنمية والإدماج',
      icon: '🇲🇦'
    },
    {
      name: 'جماعة الدروة - إقليم برشيد',
      role: 'السلطات المحلية والجماعية',
      icon: '🏢'
    },
    {
      name: 'وزارة الشباب والثقافة والتواصل',
      role: 'الوصاية القطاعية',
      icon: '⭐'
    },
    {
      name: 'الهلال الأحمر المغربي',
      role: 'شريك الإسعاف والتأطير الصحي',
      icon: '🚑'
    }
  ]
};
