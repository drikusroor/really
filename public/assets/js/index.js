document.addEventListener('DOMContentLoaded', function () {
  checkAuth();
});

function getCookie(cname) {
  let name = cname + "=";
  let decodedCookie = decodeURIComponent(document.cookie);
  let ca = decodedCookie.split(';');
  for(let i = 0; i <ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

function parseJwt($token) {
  let base64Url = $token.split('.')[1];
  let base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
  let jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
      return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
  }).join(''));

  return JSON.parse(jsonPayload);
}

function checkAuth() {
  const token = getCookie('jwt');

  if (!token) {
    onLoggedOut();
  }

  if (token) {
    const payload = parseJwt(token);
    if (payload.validUntil > Date.now() / 1000) {
      onAuthenticated();
    } else {
      onLoggedOut();
    }
    
    if (payload.isAdmin) {
      onAdminAuthenticated();
    }
  }
}

function onAuthenticated() {
  const onAuthElements = document.querySelectorAll('[data-on-auth]');

  onAuthElements.forEach((element) => {
    element.classList.remove('hidden');
  });
}

function onAdminAuthenticated() {
  const onAuthElements = document.querySelectorAll('[data-on-admin-auth]');

  onAuthElements.forEach((element) => {
    element.classList.remove('hidden');
  });
}

function onLoggedOut() {
  const onAuthElements = document.querySelectorAll('[data-on-logged-out]');
  onAuthElements.forEach((element) => {
    element.classList.remove('hidden');
  });
}