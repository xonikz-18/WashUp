<template>
  <main class="login-page">
    <div class="login-background"></div>

    <section class="login-content">

      <img
        src="../assets/WashUP.png"
        alt="WashUP Logo"
        class="washup-logo"
      />

      <div class="login-card">

        <div class="login-header">
          <h1>Welcome Back</h1>

          <p>
            Sign in to manage your laundry orders.
          </p>
        </div>

        <!-- Error Notification -->
        <div
          v-if="errorMessage"
          class="error-notification"
          role="alert"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <circle
              cx="12"
              cy="12"
              r="9"
            />

            <path d="M12 8v4" />
            <path d="M12 16h.01" />
          </svg>

          <span>{{ errorMessage }}</span>
        </div>

        <form
          @submit.prevent="handleLogin"
          novalidate
        >

          <!-- Username -->
          <div class="form-group">
            <label for="username">
              Username
            </label>

            <input
              id="username"
              v-model="username"
              type="text"
              placeholder="Enter your username"
              autocomplete="username"
              :class="{ 'input-error': usernameError }"
              @input="clearErrors"
            />

            <p
              v-if="usernameError"
              class="field-error"
            >
              {{ usernameError }}
            </p>
          </div>

          <!-- Password -->
          <div class="form-group">
            <label for="password">
              Password
            </label>

            <div class="password-wrapper">

              <input
                id="password"
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                placeholder="Enter your password"
                autocomplete="current-password"
                :class="{ 'input-error': passwordError }"
                @input="clearErrors"
              />

              <button
                type="button"
                class="password-toggle"
                @click="togglePassword"
                :aria-label="
                  showPassword
                    ? 'Hide password'
                    : 'Show password'
                "
              >

                <!-- OPEN EYE = PASSWORD VISIBLE -->
                <svg
                  v-if="showPassword"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path
                    d="M2.062 12.348a1 1 0 0 1 0-.696
                    C3.248 7.102 7.398 4 12 4
                    c4.602 0 8.752 3.102 9.938 7.652
                    a1 1 0 0 1 0 .696
                    C20.752 16.898 16.602 20 12 20
                    c-4.602 0-8.752-3.102-9.938-7.652Z"
                  />

                  <circle
                    cx="12"
                    cy="12"
                    r="3"
                  />
                </svg>

                <!-- CLOSED/SLASHED EYE = PASSWORD HIDDEN -->
                <svg
                  v-else
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path d="M3 3l18 18" />

                  <path
                    d="M10.58 10.58a2 2 0 0 0 2.84 2.84"
                  />

                  <path
                    d="M9.88 4.24A9.77 9.77 0 0 1 12 4
                    c4.6 0 8.75 3.1 9.94 7.65
                    a1 1 0 0 1 0 .7
                    10.3 10.3 0 0 1-4.2 5.06"
                  />

                  <path
                    d="M6.61 6.61a10.4 10.4 0 0 0-4.55 5.04
                    1 1 0 0 0 0 .7
                    C3.25 16.9 7.4 20 12 20
                    a9.9 9.9 0 0 0 4.39-1.02"
                  />
                </svg>

              </button>

            </div>

            <p
              v-if="passwordError"
              class="field-error"
            >
              {{ passwordError }}
            </p>
          </div>

          <!-- Login Button -->
          <button
            type="submit"
            class="login-button"
            :disabled="loading"
          >
            {{ loading ? 'Logging in...' : 'Login' }}
          </button>

        </form>

        <p class="authorized-message">
          For authorized laundry shop staff only.
        </p>

      </div>
    </section>
  </main>
</template>


<script setup>
import { ref } from 'vue'

const emit = defineEmits(['login-success'])

const username = ref('')
const password = ref('')

const showPassword = ref(false)
const loading = ref(false)

const errorMessage = ref('')
const usernameError = ref('')
const passwordError = ref('')


const togglePassword = () => {
  showPassword.value = !showPassword.value
}


const clearErrors = () => {
  errorMessage.value = ''
  usernameError.value = ''
  passwordError.value = ''
}


const handleLogin = async () => {

  // Clear previous errors
  clearErrors()

  // Check empty fields
  if (!username.value.trim() && !password.value) {
    usernameError.value = 'Username is required.'
    passwordError.value = 'Password is required.'
    errorMessage.value = 'Please enter your admin username and password.'
    return
  }

  if (!username.value.trim()) {
    usernameError.value = 'Username is required.'
    errorMessage.value = 'Please enter your admin username.'
    return
  }

  if (!password.value) {
    passwordError.value = 'Password is required.'
    errorMessage.value = 'Please enter your admin password.'
    return
  }


  loading.value = true

  try {

    const response = await fetch('/api/login', {
      method: 'POST',

      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },

      body: JSON.stringify({
        username: username.value,
        password: password.value,
      }),
    })


    const data = await response.json()


    if (!response.ok) {
      throw new Error(
        data.message || 'Invalid admin username or password.'
      )
    }


    console.log('Login successful:', data)

    // Save the authentication token
    localStorage.setItem(
      'washup_token',
      data.token
    )

    // Save the logged-in user
    localStorage.setItem(
      'washup_user',
      JSON.stringify(data.user)
    )

    // Tell App.vue that login succeeded
    emit('login-success', data.user)


  } catch (error) {

    console.error('Login error:', error)

    // Stop loading immediately
    loading.value = false

    // Show error notification
    errorMessage.value =
      'Invalid admin username or password.'

    // Make both fields red
    usernameError.value =
      'Invalid username or password.'

    passwordError.value =
      'Invalid username or password.'

  } finally {

    loading.value = false

  }
}
</script>


<style scoped>

.login-page {
  position: relative;
  width: 100%;
  min-height: 100vh;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}

.login-background {
  position: absolute;
  inset: 0;
  background-image: url('../assets/loginbg.png');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
}

.login-content {
  position: relative;
  z-index: 1;
  width: 100%;
  max-width: 520px;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 32px 24px;
}

.washup-logo {
  display: block;
  width: 350px;
  max-width: 80vw;
  height: auto;
  margin-bottom: 30px;
}

.login-card {
  width: 100%;
  max-width: 400px;
  padding: 28px 30px 24px;
  background: #ffffff;
  border-radius: 14px;
  border-top: 3px solid #2699b9;
  box-shadow:
    0 8px 24px rgba(0, 0, 0, 0.12);
}

.login-header h1 {
  margin: 0 0 5px;
  font-size: 22px;
  font-weight: 700;
  color: #182238;
}

.login-header p {
  margin: 0 0 20px;
  font-size: 11px;
  color: #75839b;
}


/* ============================= */
/* ERROR NOTIFICATION             */
/* ============================= */

.error-notification {
  display: flex;
  align-items: center;
  gap: 8px;

  margin-bottom: 18px;
  padding: 10px 11px;

  background: #fff1f2;
  border: 1px solid #fca5a5;
  border-radius: 7px;

  color: #dc2626;

  font-size: 10px;
  font-weight: 600;

  animation: errorSlideIn 0.2s ease;
}

.error-notification svg {
  flex-shrink: 0;
  width: 15px;
  height: 15px;
}

@keyframes errorSlideIn {
  from {
    opacity: 0;
    transform: translateY(-4px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}


.form-group {
  margin-bottom: 18px;
}

.form-group label {
  display: block;
  margin-bottom: 7px;
  font-size: 10px;
  font-weight: 600;
  color: #344054;
}

.form-group input {
  width: 100%;
  height: 34px;
  padding: 0 11px;
  box-sizing: border-box;

  border: 1px solid #d9e0e8;
  border-radius: 6px;

  background: #f8fafc;

  font-family: inherit;
  font-size: 10px;
  color: #344054;

  outline: none;

  transition:
    border-color 0.2s ease,
    background 0.2s ease,
    box-shadow 0.2s ease;
}

.form-group input::placeholder {
  color: #a5b1c2;
}

.form-group input:focus {
  background: #ffffff;

  border-color: #2699b9;

  box-shadow:
    0 0 0 2px rgba(38, 153, 185, 0.12);
}


/* ============================= */
/* RED ERROR INPUT                */
/* ============================= */

.form-group input.input-error {
  border-color: #ef4444;
  background: #fff7f7;
}

.form-group input.input-error:focus {
  border-color: #ef4444;

  box-shadow:
    0 0 0 2px rgba(239, 68, 68, 0.12);
}

.field-error {
  margin: 5px 0 0;

  font-size: 8px;
  color: #dc2626;
  font-weight: 500;
}


/* ============================= */
/* PASSWORD                       */
/* ============================= */

.password-wrapper {
  position: relative;
}

.password-wrapper input {
  padding-right: 40px;
}

.password-toggle {
  position: absolute;
  top: 50%;
  right: 9px;

  width: 24px;
  height: 24px;
  padding: 0;

  transform: translateY(-50%);

  display: flex;
  align-items: center;
  justify-content: center;

  border: none;
  background: transparent;

  color: #8b99ac;

  cursor: pointer;
}

.password-toggle:hover {
  color: #2699b9;
}

.password-toggle svg {
  width: 15px;
  height: 15px;
}


/* ============================= */
/* LOGIN BUTTON                   */
/* ============================= */

.login-button {
  width: 100%;
  height: 34px;
  margin-top: 2px;

  border: none;
  border-radius: 7px;

  background: linear-gradient(
    135deg,
    #2087a6,
    #269dbd
  );

  color: #ffffff;

  font-family: inherit;
  font-size: 10px;
  font-weight: 600;

  cursor: pointer;

  box-shadow:
    0 4px 9px rgba(32, 135, 166, 0.25);

  transition:
    transform 0.15s ease,
    box-shadow 0.15s ease,
    opacity 0.15s ease;
}

.login-button:hover:not(:disabled) {
  transform: translateY(-1px);

  box-shadow:
    0 6px 12px rgba(32, 135, 166, 0.3);
}

.login-button:active:not(:disabled) {
  transform: translateY(0);
}

.login-button:disabled {
  opacity: 0.65;
  cursor: not-allowed;
}


/* ============================= */
/* AUTHORIZED MESSAGE             */
/* ============================= */

.authorized-message {
  margin: 17px 0 0;

  text-align: center;

  font-size: 8px;
  color: #a5b2c1;
}

</style>