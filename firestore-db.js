import { initializeApp } from 'firebase/app';
import { 
  getFirestore, 
  doc, 
  getDoc, 
  getDocs, 
  setDoc, 
  addDoc, 
  updateDoc, 
  deleteDoc, 
  collection, 
  getDocFromServer 
} from 'firebase/firestore';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Operation types conforming to skill guidelines
export const OperationType = {
  CREATE: 'create',
  UPDATE: 'update',
  DELETE: 'delete',
  LIST: 'list',
  GET: 'get',
  WRITE: 'write',
};

export function handleFirestoreError(error, operationType, docPath) {
  const errInfo = {
    error: error instanceof Error ? error.message : String(error),
    operationType,
    path: docPath,
    authInfo: {
      userId: null,
      email: null,
      emailVerified: null,
      isAnonymous: true,
      tenantId: null,
      providerInfo: []
    }
  };
  console.error('[Firestore Error]', JSON.stringify(errInfo));
  return errInfo;
}

let firebaseApp = null;
let firestoreDb = null;
let isConnected = false;
let configData = null;

try {
  const cfgPath = path.join(__dirname, 'firebase-applet-config.json');
  if (fs.existsSync(cfgPath)) {
    configData = JSON.parse(fs.readFileSync(cfgPath, 'utf8'));
    firebaseApp = initializeApp(configData);
    firestoreDb = getFirestore(firebaseApp, configData.firestoreDatabaseId);
    console.log('[Firestore Service] Initialized with DB:', configData.firestoreDatabaseId);
  }
} catch (err) {
  console.warn('[Firestore Service] Initialization warning:', err.message);
}

export const dbInstance = firestoreDb;
export const appConfig = configData;

// Validate Firestore connection on boot as mandated by skill
export async function testFirestoreConnection() {
  if (!firestoreDb) return { ok: false, message: 'Firestore not configured' };
  try {
    const testDocRef = doc(firestoreDb, 'test', 'connection');
    await setDoc(testDocRef, { 
      status: 'healthy', 
      updatedAt: new Date().toISOString(),
      service: 'ASLD Cloud Backend'
    }, { merge: true });
    const snap = await getDocFromServer(testDocRef);
    isConnected = true;
    return { ok: true, data: snap.data() };
  } catch (err) {
    isConnected = false;
    handleFirestoreError(err, OperationType.WRITE, 'test/connection');
    return { ok: false, error: err.message };
  }
}

// Initial Seeder: Seeds Firestore with initial items if collections are empty
export async function syncAndSeedFirestore(localDb) {
  if (!firestoreDb) return;

  try {
    // 1. Seed Projects
    const projectsCol = collection(firestoreDb, 'projects');
    const projectSnap = await getDocs(projectsCol);
    if (projectSnap.empty && localDb.projects && localDb.projects.length > 0) {
      console.log('[Firestore] Seeding initial projects...');
      for (const p of localDb.projects) {
        await setDoc(doc(firestoreDb, 'projects', String(p.id)), {
          ...p,
          syncedAt: new Date().toISOString()
        });
      }
      console.log(`[Firestore] Seeded ${localDb.projects.length} projects.`);
    } else if (!projectSnap.empty) {
      // Load remote projects into local memory
      const remoteProjects = [];
      projectSnap.forEach(d => {
        remoteProjects.push({ id: isNaN(d.id) ? d.id : Number(d.id), ...d.data() });
      });
      if (remoteProjects.length > 0) {
        localDb.projects = remoteProjects;
      }
    }

    // 2. Seed / Sync Users
    const usersCol = collection(firestoreDb, 'users');
    const userSnap = await getDocs(usersCol);
    if (userSnap.empty && localDb.users && localDb.users.length > 0) {
      console.log('[Firestore] Seeding initial users...');
      for (const u of localDb.users) {
        // Never store raw password in plaintext; store user profile
        const { password, ...safeUser } = u;
        await setDoc(doc(firestoreDb, 'users', String(u.id)), {
          ...safeUser,
          syncedAt: new Date().toISOString()
        });
      }
      console.log(`[Firestore] Seeded ${localDb.users.length} user profiles.`);
    }

    // 3. Honorary Members
    const honoraryCol = collection(firestoreDb, 'honorary_members');
    const honorarySnap = await getDocs(honoraryCol);
    if (!honorarySnap.empty) {
      const remoteMembers = [];
      honorarySnap.forEach(d => {
        remoteMembers.push({ id: isNaN(d.id) ? d.id : Number(d.id), ...d.data() });
      });
      localDb.honorary_members = remoteMembers;
    }

    // 4. Articles
    const articlesCol = collection(firestoreDb, 'articles');
    const articleSnap = await getDocs(articlesCol);
    if (!articleSnap.empty) {
      const remoteArticles = [];
      articleSnap.forEach(d => {
        remoteArticles.push({ id: isNaN(d.id) ? d.id : Number(d.id), ...d.data() });
      });
      if (remoteArticles.length > 0) {
        localDb.articles = remoteArticles;
      }
    }

    // 5. Workshops
    const workshopsCol = collection(firestoreDb, 'workshops');
    const workshopSnap = await getDocs(workshopsCol);
    if (!workshopSnap.empty) {
      const remoteWorkshops = [];
      workshopSnap.forEach(d => {
        remoteWorkshops.push({ id: isNaN(d.id) ? d.id : Number(d.id), ...d.data() });
      });
      if (remoteWorkshops.length > 0) {
        localDb.workshops = remoteWorkshops;
      }
    }

    console.log('[Firestore] Live database synchronization complete.');
  } catch (err) {
    handleFirestoreError(err, OperationType.LIST, 'seed_sync');
  }
}

// Database helper functions to persist changes
export async function persistProject(project) {
  if (!firestoreDb) return;
  try {
    const docId = String(project.id);
    await setDoc(doc(firestoreDb, 'projects', docId), {
      ...project,
      updatedAt: new Date().toISOString()
    }, { merge: true });
  } catch (err) {
    handleFirestoreError(err, OperationType.WRITE, `projects/${project.id}`);
  }
}

export async function removeProject(projectId) {
  if (!firestoreDb) return;
  try {
    await deleteDoc(doc(firestoreDb, 'projects', String(projectId)));
  } catch (err) {
    handleFirestoreError(err, OperationType.DELETE, `projects/${projectId}`);
  }
}

export async function persistUser(user) {
  if (!firestoreDb) return;
  try {
    const { password, ...safeUser } = user;
    await setDoc(doc(firestoreDb, 'users', String(user.id)), {
      ...safeUser,
      updatedAt: new Date().toISOString()
    }, { merge: true });
  } catch (err) {
    handleFirestoreError(err, OperationType.WRITE, `users/${user.id}`);
  }
}

export async function persistProposal(proposal) {
  if (!firestoreDb) return;
  try {
    await setDoc(doc(firestoreDb, 'proposals', String(proposal.id)), {
      ...proposal,
      createdAt: new Date().toISOString()
    }, { merge: true });
  } catch (err) {
    handleFirestoreError(err, OperationType.WRITE, `proposals/${proposal.id}`);
  }
}

export async function persistContactMessage(msg) {
  if (!firestoreDb) return;
  try {
    await addDoc(collection(firestoreDb, 'contact_messages'), {
      ...msg,
      createdAt: new Date().toISOString()
    });
  } catch (err) {
    handleFirestoreError(err, OperationType.CREATE, 'contact_messages');
  }
}

export async function persistHonoraryMember(member) {
  if (!firestoreDb) return;
  try {
    await setDoc(doc(firestoreDb, 'honorary_members', String(member.id)), {
      ...member,
      createdAt: new Date().toISOString()
    }, { merge: true });
  } catch (err) {
    handleFirestoreError(err, OperationType.WRITE, `honorary_members/${member.id}`);
  }
}

export async function removeHonoraryMember(memberId) {
  if (!firestoreDb) return;
  try {
    await deleteDoc(doc(firestoreDb, 'honorary_members', String(memberId)));
  } catch (err) {
    handleFirestoreError(err, OperationType.DELETE, `honorary_members/${memberId}`);
  }
}

export async function persistArticle(article) {
  if (!firestoreDb) return;
  try {
    await setDoc(doc(firestoreDb, 'articles', String(article.id)), {
      ...article,
      updatedAt: new Date().toISOString()
    }, { merge: true });
  } catch (err) {
    handleFirestoreError(err, OperationType.WRITE, `articles/${article.id}`);
  }
}

export async function removeArticle(articleId) {
  if (!firestoreDb) return;
  try {
    await deleteDoc(doc(firestoreDb, 'articles', String(articleId)));
  } catch (err) {
    handleFirestoreError(err, OperationType.DELETE, `articles/${articleId}`);
  }
}

export async function persistWorkshop(workshop) {
  if (!firestoreDb) return;
  try {
    await setDoc(doc(firestoreDb, 'workshops', String(workshop.id)), {
      ...workshop,
      updatedAt: new Date().toISOString()
    }, { merge: true });
  } catch (err) {
    handleFirestoreError(err, OperationType.WRITE, `workshops/${workshop.id}`);
  }
}

export async function removeWorkshop(workshopId) {
  if (!firestoreDb) return;
  try {
    await deleteDoc(doc(firestoreDb, 'workshops', String(workshopId)));
  } catch (err) {
    handleFirestoreError(err, OperationType.DELETE, `workshops/${workshopId}`);
  }
}

