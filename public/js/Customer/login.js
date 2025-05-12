const formTitle = document.getElementById("form-title");
const toggleText = document.getElementById("toggle-text");
const toggleBtn = document.getElementById("toggle-btn");
const authForm = document.getElementById("auth-form");
let isLogin = true;

toggleBtn.addEventListener("click", (e) => {
  e.preventDefault();
  isLogin = !isLogin;
  formTitle.innerText = isLogin ? "Login" : "Register";
  toggleText.innerHTML = isLogin
    ? `Don't have an account? <a href="#" id="toggle-btn">Register</a>`
    : `Already have an account? <a href="#" id="toggle-btn">Login</a>`;
  document.getElementById("toggle-btn").addEventListener("click", toggleBtn.click);
});

// Handle login/register form submission
authForm.addEventListener("submit", (e) => {
  e.preventDefault();
  const username = document.getElementById("username").value;
  alert(`${isLogin ? "Logging in" : "Registering"} as ${username}`);
});

// Google Login Callback
function handleCredentialResponse(response) {
  const data = jwt_decode(response.credential);
  alert(`Google Logged in as ${data.name}`);
}
