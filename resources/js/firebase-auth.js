import { initializeApp } from "firebase/app";
import {
  getAuth,
  signInWithPopup,
  signInWithRedirect,
  getRedirectResult,
  GoogleAuthProvider,
  GithubAuthProvider,
} from "firebase/auth";
import axios from "axios";

/* =========================
   Firebase init
========================= */

const firebaseConfig = {
  apiKey: import.meta.env.VITE_FIREBASE_API_KEY,
  authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN,
  projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID,
  storageBucket: import.meta.env.VITE_FIREBASE_STORAGE_BUCKET,
  messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID,
  appId: import.meta.env.VITE_FIREBASE_APP_ID,
};

const app = initializeApp(firebaseConfig);
export const auth = getAuth(app);

/* =========================
   Helpers / Providers
========================= */

const IGNORED_AUTH_ERRORS = new Set([
  "auth/popup-closed-by-user",
  "auth/cancelled-popup-request",
]);

// ✅ Em alguns navegadores o popup demora pra “morrer”.
// A UI deve poder destravar rápido, então aqui usamos só um throttle curto
// (anti clique duplo) e NÃO um lock longo via promise.
let lastAttemptAt = 0;
const ATTEMPT_THROTTLE_MS = 450;

const providers = {
  google: (() => {
    const p = new GoogleAuthProvider();
    p.setCustomParameters({ prompt: "select_account" });
    return p;
  })(),
  github: (() => {
    const p = new GithubAuthProvider();
    p.addScope("user:email");
    return p;
  })(),
};

function getProvider(providerName) {
  return providers[providerName] ?? null;
}

function friendlyAuthMessage(code, fallbackMsg = "Falha ao autenticar.") {
  switch (code) {
    case "auth/account-exists-with-different-credential":
      return "Esse email já está cadastrado com outro método de login.";
    case "auth/popup-blocked":
      return "O navegador bloqueou o popup de login. Tente permitir popups ou use redirecionamento.";
    case "auth/network-request-failed":
      return "Falha de rede. Verifique sua conexão e tente novamente.";
    case "auth/unauthorized-domain":
      return "Domínio não autorizado no Firebase. Verifique 'Authorized domains' no Firebase Console.";
    case "auth/operation-not-allowed":
      return "Provider não habilitado no Firebase (Google/GitHub). Ative no Firebase Console.";
    default:
      return fallbackMsg;
  }
}

async function finalizeBackendLogin({ user, providerName, backendUrl, redirectTo }) {
  const idToken = await user.getIdToken(true);

  await axios.post(
    backendUrl,
    { idToken, provider: providerName },
    {
      timeout: 15_000,
      headers: {
        // opcional — se seu backend valida por header
        Authorization: `Bearer ${idToken}`,
      },
    }
  );

  window.location.assign(redirectTo);
  return { ok: true, user };
}

/* =========================
   Redirect consumption (fallback)
========================= */

export async function consumeRedirectResult({
  backendUrl = "/auth/social/login",
  redirectTo = "/dashboard",
} = {}) {
  const result = await getRedirectResult(auth);

  // Não houve redirect login
  if (!result?.user) return { ok: false, redirected: false };

  // tenta inferir o provider
  const providerId =
    result?.providerId ||
    result?.credential?.providerId ||
    result?.user?.providerData?.[0]?.providerId;

  const providerName =
    providerId === "google.com"
      ? "google"
      : providerId === "github.com"
      ? "github"
      : "unknown";

  return finalizeBackendLogin({
    user: result.user,
    providerName,
    backendUrl,
    redirectTo,
  });
}

/* =========================
   Social login
========================= */

export async function socialLogin(
  providerName,
  {
    redirectTo = "/dashboard",
    useRedirectFallback = true,
    backendUrl = "/auth/social/login",
  } = {}
) {
  // ✅ anti clique duplo, sem prender o usuário por vários segundos
  const now = Date.now();
  if (now - lastAttemptAt < ATTEMPT_THROTTLE_MS) {
    return { ok: false, ignored: true, code: "client/throttled" };
  }
  lastAttemptAt = now;

  const provider = getProvider(providerName);
  if (!provider) return { ok: false, ignored: false, code: "unsupported-provider" };

  try {
    const result = await signInWithPopup(auth, provider);

    return await finalizeBackendLogin({
      user: result.user,
      providerName,
      backendUrl,
      redirectTo,
    });
  } catch (err) {
    const code = err?.code;

    // cancelamentos comuns (não fatal)
    if (IGNORED_AUTH_ERRORS.has(code)) {
      return { ok: false, ignored: true, code };
    }

    // popup bloqueado -> redirect (Safari/iOS)
    if (code === "auth/popup-blocked" && useRedirectFallback) {
      await signInWithRedirect(auth, provider);
      return { ok: false, redirected: true, code };
    }

    console.error("socialLogin error:", code, err);

    const message = friendlyAuthMessage(code, err?.message);
    const e = new Error(message);
    e.code = code;
    e.original = err;
    throw e;
  }
}
