document.addEventListener('DOMContentLoaded', function () {
  checkAuth();
});

function getCookie(cname) {
  let name = cname + "=";
  console.log(document.cookie);
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

function checkAuth() {
  const token = getCookie('jwt');
  if (token) {
    const payload = parseJwt(token);
    if (payload.validUntil > Date.now() / 1000) {
      onAuthenticated();
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