<template>
  <main class="login-page">

    <section class="login-left">

      <div class="brand">
        <div class="brand-logo">
          <img
            src="../assets/washup-logo.png"
            alt="WashUp logo"
          />
        </div>

        <div class="brand-text">
          <h1>WashUp</h1>
          <p>Laundry Management System</p>
        </div>
      </div>


      <div class="illustration">
        <img
          src="../assets/washup-logo.png"
          alt="Laundry machine illustration"
        />
      </div>


      <div class="intro">
        <h2>
          Simple laundry management<br />
          made easy.
        </h2>

        <p>
          Manage orders, track laundry status, and keep every
          transaction organized in one place.
        </p>
      </div>

    </section>


    <section class="login-right">

      <div class="login-content">

        <!-- Login Card -->
        <div class="login-card">

          <p class="description">
            Sign in to manage your laundry orders.
          </p>


          <form @submit.prevent="handleLogin">

            <div class="form-group">
              <label for="username">
                Username
              </label>

              <input
                id="username"
                v-model.trim="form.username"
                type="text"
                placeholder="Enter your username"
                autocomplete="username"
                required
              />
            </div>

            <div class="form-group password-group">

              <label for="password">
                Password
              </label>

              <div class="password-wrapper">

                <input
                  id="password"
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  placeholder="Enter your password"
                  autocomplete="current-password"
                  required
                />

                <button
                  type="button"
                  class="password-toggle"
                  @click="showPassword = !showPassword"
                  :aria-label="
                    showPassword
                      ? 'Hide password'
                      : 'Show password'
                  "
                >

                  <svg
                    v-if="!showPassword"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                  >
                    <path
                      d="M2.5 12s3.5-5.5 9.5-5.5S21.5 12 21.5 12 18 17.5 12 17.5 2.5 12 2.5 12Z"
                    />

                    <circle
                      cx="12"
                      cy="12"
                      r="2.5"
                    />
                  </svg>

                  <!-- Eye closed -->
                  <svg
                    v-else
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                  >
                    <path
                      d="M3 3l18 18"
                    />

                    <path
                      d="M10.6 6.7C11.05 6.57 11.52 6.5 12 6.5c6 0 9.5 5.5 9.5 5.5a17 17 0 0 1-3.1 3.45"
                    />

                    <path
                      d="M6.1 8.1C3.8 9.65 2.5 12 2.5 12s3.5 5.5 9.5 5.5c1.1 0 2.1-.18 3-.5"
                    />
                  </svg>

                </button>

              </div>
            </div>


            <p
              v-if="errorMessage"
              class="error-message"
            >
              {{ errorMessage }}
            </p>


            <button
              type="submit"
              class="login-button"
              :disabled="isLoading"
            >
              {{ isLoading ? 'Logging in...' : 'Login' }}
            </button>

          </form>

        </div>


        <p class="authorized-message">
          For authorized laundry shop staff only.
        </p>

      </div>

    </section>

  </main>
</template>


<script setup>
import { reactive, ref } from 'vue'

const form = reactive({
  username: '',
  password: ''
})

const showPassword = ref(false)
const isLoading = ref(false)
const errorMessage = ref('')


async function handleLogin() {
  errorMessage.value = ''

  if (!form.username || !form.password) {
    errorMessage.value =
      'Please enter your username and password.'
    return
  }

  isLoading.value = true

  try {

      const response = await fetch(
        'http://localhost:3000/api/login',
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            username: form.username,
            password: form.password
          })
        }
      )

      const data = await response.json()

      if (!response.ok) {
        throw new Error(data.message)
      }

    console.log('Login submitted')

    await new Promise(resolve =>
      setTimeout(resolve, 800)
    )

  } catch (error) {

    errorMessage.value =
      error.message ||
      'Something went wrong. Please try again.'

  } finally {

    isLoading.value = false

  }
}
</script>


<style scoped>

.login-page {
  width: 100%;
  min-height: 100vh;

  display: grid;
  grid-template-columns: 1fr 1fr;

  background: #f7f9fb;
}


.login-left {
  position: relative;

  min-height: 100vh;

  background: #23809b;
  color: white;

  padding: 42px 6%;
}

.brand {
  position: absolute;

  top: 42px;
  left: 6%;

  display: flex;
  align-items: center;

  gap: 10px;
}

.brand-logo {
  width: 40px;
  height: 40px;

  display: flex;
  align-items: center;
  justify-content: center;

  background: white;

  border-radius: 9px;

  overflow: hidden;
}

.brand-logo img {
  width: 30px;
  height: 30px;

  object-fit: contain;
}

.brand-text h1 {
  margin: 0;

  font-size: 21px;
  line-height: 1;

  font-weight: 700;
}

.brand-text p {
  margin: 5px 0 0;

  font-size: 10px;
  line-height: 1;

  opacity: 0.9;
}

.illustration {
  position: absolute;

  top: 43%;
  left: 50%;

  width: min(400px, 32vw);
  height: min(400px, 32vw);

  transform: translate(-50%, -50%);

  display: flex;
  align-items: center;
  justify-content: center;

  background: white;

  border-radius: 50%;
}

.illustration img {
  width: 100%;
  height: 100%;

  object-fit: contain;
}

.intro {
  position: absolute;

  left: 6%;
  bottom: 55px;

  width: 88%;
  max-width: 520px;
}

.intro h2 {
  margin: 0 0 15px;

  font-size: clamp(23px, 2vw, 30px);
  line-height: 1.22;

  font-weight: 700;
}

.intro p {
  max-width: 500px;

  margin: 0;

  font-size: 12px;
  line-height: 1.65;

  opacity: 0.92;
}


.login-right {
  min-height: 100vh;

  display: flex;
  align-items: flex-start;
  justify-content: center;

  padding: 42px 6%;
}


.login-content {
  width: 100%;
  max-width: 430px;
  margin-top: 160px;

  display: flex;
  flex-direction: column;
  align-items: center;
}


.login-card {
  width: 100%;

  padding: 32px 30px 30px;

  background: #ffffff;

  border: 1px solid #e0e6eb;

  border-radius: 11px;

  box-shadow:
    0 3px 10px rgba(0, 0, 0, 0.035);
}


.description {
  margin: 0 0 24px;

  color: #667c98;

  font-size: 12px;
  line-height: 1.5;
}


.form-group {
  margin-bottom: 21px;
}

.form-group label {
  display: block;

  margin-bottom: 8px;

  color: #40516a;

  font-size: 12px;
  font-weight: 600;
}


.form-group input {
  width: 100%;
  height: 39px;

  padding: 0 12px;

  border: 1px solid #d4dde6;
  border-radius: 6px;

  background: #ffffff;

  color: #43536a;

  font-size: 11px;

  outline: none;

  transition:
    border-color 0.2s ease,
    box-shadow 0.2s ease;
}

.form-group input::placeholder {
  color: #a6b1bf;
}

.form-group input:focus {
  border-color: #23809b;

  box-shadow:
    0 0 0 3px rgba(35, 128, 155, 0.1);
}


.password-wrapper {
  position: relative;

  width: 100%;
}

.password-wrapper input {
  padding-right: 42px;
}


.password-toggle {
  position: absolute;

  top: 50%;
  right: 9px;

  width: 27px;
  height: 27px;

  padding: 0;

  display: flex;
  align-items: center;
  justify-content: center;

  transform: translateY(-50%);

  border: none;

  background: transparent;

  color: #8ca0b5;

  cursor: pointer;
}

.password-toggle:hover {
  color: #23809b;
}

.password-toggle svg {
  width: 17px;
  height: 17px;

  fill: none;

  stroke: currentColor;

  stroke-width: 1.5;

  stroke-linecap: round;
  stroke-linejoin: round;
}

.error-message {
  margin: -5px 0 14px;

  color: #c0392b;

  font-size: 10px;
}


.login-button {
  width: 100%;
  height: 39px;

  border: none;
  border-radius: 6px;

  background: #23809b;

  color: white;

  font-size: 12px;
  font-weight: 600;

  cursor: pointer;

  transition:
    background 0.2s ease,
    transform 0.1s ease;
}

.login-button:hover {
  background: #1d7088;
}

.login-button:active {
  transform: scale(0.99);
}

.login-button:disabled {
  opacity: 0.65;

  cursor: not-allowed;
}


.authorized-message {
  margin: 15px 0 0;

  color: #a5b2c1;

  font-size: 10px;

  text-align: center;
}


@media (max-width: 900px) {

  .login-page {
    grid-template-columns: 1fr;
  }

  .login-left {
    min-height: 650px;
  }

  .brand {
    left: 30px;
  }

  .illustration {
    width: 270px;
    height: 270px;
  }

  .intro {
    left: 30px;
    bottom: 40px;
    width: calc(100% - 60px);
  }

  .login-right {
    min-height: auto;

    padding: 60px 30px;
  }

  .login-content {
    margin-top: 0;
  }
}

@media (max-width: 500px) {

  .login-left {
    min-height: 580px;

    padding: 25px;
  }

  .brand {
    top: 25px;
    left: 25px;
  }

  .illustration {
    width: 220px;
    height: 220px;
  }

  .intro {
    left: 25px;
    bottom: 30px;

    width: calc(100% - 50px);
  }

  .intro h2 {
    font-size: 21px;
  }

  .intro p {
    font-size: 11px;
  }

  .login-right {
    padding: 40px 20px;
  }

  .login-card {
    padding: 27px 22px;
  }
}

</style>