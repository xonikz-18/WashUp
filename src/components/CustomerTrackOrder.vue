<template>
  <main class="customer-page">

    <!-- Same background used by Login.vue -->
    <div class="customer-background"></div>

    <div class="customer-content">

      <!-- Main Tracker -->
      <section
        ref="trackerSection"
        class="tracker-section"
      >

        <!-- Exact WashUP logo used by Login.vue -->
        <img
          src="../assets/WashUP.png"
          alt="WashUP Logo"
          class="hero-logo"
        />

        <p class="hero-subtitle">
          Enter your Order Number to check the status of your laundry.
        </p>


        <!-- Search -->
        <div class="search-card">

          <div class="search-input-wrapper">

            <svg
              class="search-icon"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <circle
                cx="11"
                cy="11"
                r="7"
              />

              <path d="m20 20-4-4" />
            </svg>

            <input
              v-model="orderNumber"
              type="text"
              placeholder="Enter your order number"
              autocomplete="off"
              :disabled="loading"
              @keyup.enter="trackOrder"
            />

          </div>


          <button
            type="button"
            class="track-button"
            :disabled="loading"
            @click="trackOrder"
          >

            <span
              v-if="loading"
              class="spinner"
            ></span>

            <svg
              v-else
              class="track-arrow"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <path d="M5 12h14" />
              <path d="m13 6 6 6-6 6" />
            </svg>

            {{ loading ? 'Tracking...' : 'Track' }}

          </button>

        </div>


        <p
          v-if="errorMessage"
          class="search-error"
        >
          {{ errorMessage }}
        </p>


        <!-- =========================
             ORDER FOUND
        ========================== -->
        <section
          v-if="order"
          class="result-section"
        >

          <div class="order-card">

            <!-- Order Header -->
            <div class="order-heading">

              <div>

                <h2>
                  Order {{ order.order_number }}
                </h2>

                <p class="order-summary">

                  Customer:
                  {{ order.customer_name }}

                  <span>•</span>

                  Service:
                  {{ order.service?.service_name || '—' }}

                  <span>•</span>

                  Weight:
                  {{ formatWeight(order.weight) }} kg

                </p>

              </div>


              <!-- Status -->
              <div
                class="status-badge"
                :class="statusClass(order.status)"
              >

                <span class="status-dot"></span>

                {{ order.status }}

              </div>

            </div>


            <!-- Order Information -->
            <div class="details-grid">

              <div class="detail-card">

                <span>
                  Estimated Pickup
                </span>

                <strong>
                  {{ formatPickup(order.estimated_pickup) }}
                </strong>

              </div>


              <div class="detail-card">

                <span>
                  Total Cost
                </span>

                <strong>
                  ₱{{ formatMoney(order.total_price) }}
                </strong>

              </div>

            </div>


            <!-- Status Progress -->
            <div class="status-tracker">

              <!-- Pending -->
              <div
                class="tracker-step"
                :class="{
                  active: statusStep >= 1,
                  current: order.status === 'Pending'
                }"
              >

                <div class="step-circle">

                  <svg
                    v-if="statusStep > 1"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                  >
                    <path d="m5 12 4 4L19 6" />
                  </svg>

                  <span v-else></span>

                </div>

                <span class="step-label">
                  Pending
                </span>

              </div>


              <!-- Line -->
              <div
                class="tracker-line"
                :class="{ active: statusStep >= 2 }"
              ></div>


              <!-- Ready -->
              <div
                class="tracker-step"
                :class="{
                  active: statusStep >= 2,
                  current: order.status === 'Ready for Pickup'
                }"
              >

                <div class="step-circle">

                  <svg
                    v-if="statusStep > 2"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                  >
                    <path d="m5 12 4 4L19 6" />
                  </svg>

                  <span v-else></span>

                </div>

                <span class="step-label">
                  Ready for Pickup
                </span>

              </div>


              <!-- Line -->
              <div
                class="tracker-line"
                :class="{ active: statusStep >= 3 }"
              ></div>


              <!-- Claimed -->
              <div
                class="tracker-step"
                :class="{
                  active: statusStep >= 3,
                  current: order.status === 'Claimed'
                }"
              >

                <div class="step-circle">

                  <svg
                    v-if="statusStep >= 3"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                  >
                    <path d="m5 12 4 4L19 6" />
                  </svg>

                  <span v-else></span>

                </div>

                <span class="step-label">
                  Claimed
                </span>

              </div>

            </div>


            <!-- Current Status Message -->
            <p class="status-message">
              {{ statusMessage }}
            </p>

          </div>


          <!-- Give Feedback -->
          <div class="feedback-action">

            <button
              v-if="!order.has_feedback"
              type="button"
              class="feedback-button"
              @click="openFeedback"
            >
              <span>★</span>
              Give Feedback
            </button>

            <button
              v-else
              type="button"
              class="feedback-button feedback-submitted-button"
              disabled
            >
              <span>✓</span>
              Feedback Submitted
            </button>

          </div>

        </section>


        <!-- =========================
             ORDER NOT FOUND
        ========================== -->
        <section
          v-else-if="searched && !loading"
          class="not-found-card"
        >

          <div class="not-found-icon">

            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <circle
                cx="11"
                cy="11"
                r="7"
              />

              <path d="m20 20-4-4" />

              <path d="M8.5 8.5 13.5 13.5" />
              <path d="M13.5 8.5 8.5 13.5" />
            </svg>

          </div>


          <h2>
            Order Not Found
          </h2>


          <p>
            We couldn't find an order with that Order Number.
            Please check the number and try again.
          </p>


          <button
            type="button"
            class="try-again-button"
            @click="resetSearch"
          >
            Try Again
          </button>

        </section>

      </section>

    </div>


    <!-- =========================
     FEEDBACK MODAL
========================= -->
<div
  v-if="feedbackVisible"
  class="modal-overlay"
  @click.self="closeFeedback"
>
  <div class="feedback-modal">
    <!-- Header -->
    <div class="feedback-heading">

      <h2>
        Submit Feedback
      </h2>

      <p>
        Tell us how your experience was with this order.
      </p>

    </div>


    <!-- Rating -->
    <div class="feedback-row rating-row">

      <label>
        Rating
      </label>

      <div class="stars">

        <button
          v-for="star in 5"
          :key="star"
          type="button"
          class="star-button"
          :class="{
            selected:
              feedback.rating >= star
          }"
          :aria-label="`Rate ${star} out of 5`"
          @click="feedback.rating = star"
        >
          ★
        </button>

      </div>

    </div>


    <!-- Type -->
    <div class="feedback-row type-row">

      <label>
        Type
      </label>

      <div class="select-wrapper compact-select">

        <select
          v-model="feedback.type"
        >
          <option value="Feedback">
            Feedback
          </option>

          <option value="Complaint">
            Complaint
          </option>

          <option value="Suggestion">
            Suggestion
          </option>
        </select>

        <svg
          class="select-icon"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <path d="m6 9 6 6 6-6" />
        </svg>

      </div>

    </div>


    <!-- Comment -->
    <div class="feedback-row comment-row">

      <label>
        Comment
      </label>

      <textarea
        v-model="feedback.comment"
        maxlength="3000"
        placeholder="Tell us what went well or how we can improve..."
      ></textarea>

    </div>


    <!-- Error -->
    <p
      v-if="feedbackError"
      class="feedback-error"
    >
      {{ feedbackError }}
    </p>


    <!-- Footer -->
    <div class="feedback-footer">

      <p>
        Your feedback helps us improve pickup,
        quality, and service.
      </p>

      <div class="feedback-actions">
        <button
          type="button"
          class="submit-button"
          :disabled="submittingFeedback"
          @click="submitFeedback"
        >
          {{
            submittingFeedback
              ? 'Submitting...'
              : 'Submit'
          }}
        </button>

      </div>

    </div>

  </div>
</div>

    <!-- =========================
         FEEDBACK SUCCESS
    ========================== -->
    <div
      v-if="successVisible"
      class="modal-overlay"
    >

      <div class="success-modal">

        <div class="success-icon">

          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
          >
            <path d="m5 12 4 4L19 6" />
          </svg>

        </div>


        <h2>
          Feedback Submitted
        </h2>


        <p>
          Thank you for sharing your feedback with WashUP!
        </p>


        <button
          type="button"
          class="modal-button"
          @click="closeSuccess"
        >
          Done
        </button>

      </div>

    </div>

  </main>
</template>


<script setup>
import {
  computed,
  ref
} from 'vue'


/* =========================
   TRACKER STATE
========================= */

const orderNumber = ref('')
const order = ref(null)

const loading = ref(false)
const searched = ref(false)
const errorMessage = ref('')

const trackerSection = ref(null)

const feedbackVisible = ref(false)
const submittingFeedback = ref(false)
const feedbackError = ref('')

const successVisible = ref(false)


const feedback = ref({
  rating: 5,
  type: 'Feedback',
  comment: ''
})


/* =========================
   STATUS
========================= */

const statusStep = computed(() => {

  if (!order.value) {
    return 0
  }

  switch (order.value.status) {

    case 'Pending':
      return 1

    case 'Ready for Pickup':
      return 2

    case 'Claimed':
      return 3

    default:
      return 1
  }

})


const statusMessage = computed(() => {

  if (!order.value) {
    return ''
  }

  switch (order.value.status) {

    case 'Pending':
      return 'Your order is currently being processed at the shop.'

    case 'Ready for Pickup':
      return 'Your order is currently ready for pickup at the shop.'

    case 'Claimed':
      return 'Your order has been picked up. Thank you for choosing WashUP.'

    default:
      return ''
  }

})


const statusClass = (status) => {

  switch (status) {

    case 'Pending':
      return 'status-pending'

    case 'Ready for Pickup':
      return 'status-ready'

    case 'Claimed':
      return 'status-claimed'

    default:
      return ''
  }

}


/* =========================
   TRACK ORDER
========================= */

const trackOrder = async () => {

  const cleanedNumber =
    orderNumber.value
      .trim()
      .toUpperCase()


  if (!cleanedNumber) {

    errorMessage.value =
      'Please enter your Order Number.'

    order.value = null
    searched.value = false

    return
  }


  loading.value = true
  searched.value = true

  errorMessage.value = ''
  order.value = null


  try {

    const response =
      await fetch(
        `/api/track-order/${encodeURIComponent(cleanedNumber)}`,
        {
          method: 'GET',

          headers: {
            Accept: 'application/json'
          }
        }
      )


    /* Order does not exist */
    if (response.status === 404) {

      order.value = null

      return
    }


    /* Other server error */
    if (!response.ok) {

      let message =
        'Unable to track this order.'

      try {

        const data =
          await response.json()

        if (data.message) {
          message = data.message
        }

      } catch {}

      throw new Error(message)
    }


    const data =
      await response.json()


    /* Real database order */
    order.value = data

  } catch (error) {

    console.error(
      'Track order error:',
      error
    )

    errorMessage.value =
      error.message ||
      'Unable to track this order. Please try again.'

    order.value = null

  } finally {

    loading.value = false
  }

}


/* =========================
   FEEDBACK
========================= */

const openFeedback = () => {

  if (
    !order.value ||
    order.value.has_feedback
  ) {
    return
  }

  feedback.value = {
    rating: 5,
    type: 'Feedback',
    comment: ''
  }

  feedbackError.value = ''

  feedbackVisible.value = true
}


const closeFeedback = () => {

  if (submittingFeedback.value) {
    return
  }

  feedbackVisible.value = false
  feedbackError.value = ''
}


const submitFeedback = async () => {

  feedbackError.value = ''


  if (!order.value) {

    feedbackError.value =
      'Order information is missing.'

    return
  }


  if (
    !feedback.value.rating ||
    feedback.value.rating < 1 ||
    feedback.value.rating > 5
  ) {

    feedbackError.value =
      'Please select a rating from 1 to 5.'

    return
  }


  if (!feedback.value.comment.trim()) {

    feedbackError.value =
      'Please enter a comment.'

    return
  }


  submittingFeedback.value = true


  try {

    const response =
      await fetch(
        '/api/feedbacks',
        {
          method: 'POST',

          headers: {
            Accept: 'application/json',

            'Content-Type':
              'application/json'
          },

          body: JSON.stringify({
            order_number:
              order.value.order_number,

            rating:
              feedback.value.rating,

            type:
              feedback.value.type,

            comment:
              feedback.value.comment.trim()
          })
        }
      )


    const data =
      await response.json()


    if (!response.ok) {

      throw new Error(
        data.message ||
        'Unable to submit your feedback.'
      )

    }


    feedbackVisible.value = false

    order.value = {
      ...order.value,
      has_feedback: true
    }

    successVisible.value = true

  } catch (error) {

    console.error(
      'Submit feedback error:',
      error
    )

    feedbackError.value =
      error.message ||
      'Unable to submit feedback. Please try again.'

  } finally {

    submittingFeedback.value = false
  }

}


/* =========================
   FORMATTERS
========================= */

const formatWeight = (weight) => {

  const value = Number(weight)

  if (Number.isNaN(value)) {
    return '—'
  }

  return value.toFixed(1)
}


const formatMoney = (amount) => {

  const value = Number(amount)

  if (Number.isNaN(value)) {
    return '0.00'
  }

  return value.toFixed(2)
}


const formatPickup = (dateTime) => {

  if (!dateTime) {
    return '—'
  }


  const date =
    new Date(dateTime)


  if (Number.isNaN(date.getTime())) {
    return dateTime
  }


  return (
    date.toLocaleDateString(
      'en-PH',
      {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
      }
    ) +
    ', ' +
    date.toLocaleTimeString(
      'en-PH',
      {
        hour: 'numeric',
        minute: '2-digit'
      }
    )
  )

}


/* =========================
   UI
========================= */

const resetSearch = () => {

  orderNumber.value = ''

  order.value = null

  searched.value = false

  errorMessage.value = ''
}


const closeSuccess = () => {

  successVisible.value = false
}

</script>


<style scoped>

/* =========================
   PAGE
========================= */

.customer-page {

  position: relative;

  width: 100%;

  min-height: 100vh;

  overflow-x: hidden;

  display: flex;

  justify-content: center;

  background: #ffffff;
}


.customer-background {

  position: fixed;

  inset: 0;

  z-index: 0;

  background-image:
    url('../assets/loginbg.png');

  background-size: cover;

  background-position: center;

  background-repeat: no-repeat;
}


.customer-content {

  position: relative;

  z-index: 1;

  width:
    min(
      900px,
      calc(100% - 48px)
    );

  padding-bottom: 70px;
}


/* =========================
   TRACKER
========================= */

.tracker-section {

  width: 100%;

  padding-top: 136px;

  text-align: center;
}


.hero-logo {

  display: block;

  width: 350px;
  max-width: 80vw;

  height: auto;

  margin:
    0 auto;
}


.hero-subtitle {

  margin: 6px 0 22px;

  color: #ffffff;

  font-size: 11px;

  font-weight: 400;
}


/* =========================
   SEARCH
========================= */

.search-card {

  width: 100%;

  max-width: 900px;

  margin:
    0 auto;

  padding: 18px;

  display: flex;

  align-items: center;

  gap: 12px;

  box-sizing: border-box;

  background: #ffffff;

  border-radius: 14px;

  box-shadow:
    0 8px 24px
    rgba(0,0,0,0.12);
}


.search-input-wrapper {

  position: relative;

  flex: 1;
}


.search-icon {

  position: absolute;

  top: 50%;

  left: 13px;

  width: 17px;
  height: 17px;

  transform:
    translateY(-50%);

  color: #94a3b8;

  pointer-events: none;
}


.search-input-wrapper input {

  width: 100%;

  height: 42px;

  padding:
    0 13px 0 39px;

  box-sizing: border-box;

  border:
    1px solid #d8e0e8;

  border-radius: 8px;

  outline: none;

  background: #ffffff;

  color: #344054;

  font-family: inherit;

  font-size: 11px;
}


.search-input-wrapper input::placeholder {

  color: #9ba9ba;
}


.search-input-wrapper input:focus {

  border-color: #2699b9;

  box-shadow:
    0 0 0 2px
    rgba(38,153,185,0.12);
}


.track-button {

  height: 42px;

  min-width: 150px;

  display: flex;

  align-items: center;

  justify-content: center;

  gap: 7px;

  border: none;

  border-radius: 8px;

  background:
    linear-gradient(
      135deg,
      #2087a6,
      #269dbd
    );

  color: #ffffff;

  font-family: inherit;

  font-size: 10px;
  font-weight: 600;

  cursor: pointer;

  transition:
    transform 0.15s ease,
    box-shadow 0.15s ease,
    opacity 0.15s ease;

  box-shadow:
    0 4px 9px
    rgba(32,135,166,0.25);
}


.track-button:hover:not(:disabled) {

  transform:
    translateY(-1px);

  box-shadow:
    0 6px 12px
    rgba(32,135,166,0.3);
}


.track-button:disabled {

  opacity: 0.65;

  cursor: not-allowed;
}


.track-arrow {

  width: 15px;
  height: 15px;
}


.spinner {

  width: 12px;
  height: 12px;

  border:
    2px solid
    rgba(255,255,255,0.35);

  border-top-color: #ffffff;

  border-radius: 50%;

  animation:
    spin 0.8s linear infinite;
}


.search-error {

  margin:
    10px 0 0;

  color: #ffe8e8;

  font-size: 10px;
}


/* =========================
   ORDER RESULT
========================= */

.result-section {

  margin-top: 24px;
}


.order-card {

  width: 100%;

  padding: 27px 24px 21px;

  box-sizing: border-box;

  background: #ffffff;

  border-radius: 14px;

  box-shadow:
    0 8px 24px
    rgba(0,0,0,0.12);

  text-align: left;
}


.order-heading {

  display: flex;

  align-items: flex-start;

  justify-content: space-between;

  gap: 18px;
}


.order-heading h2 {

  margin: 0;

  color: #182238;

  font-size: 17px;

  font-weight: 700;
}


.order-summary {

  margin:
    7px 0 0;

  color: #687992;

  font-size: 10px;
}


.order-summary span {

  margin:
    0 4px;

  color: #a1afbf;
}


/* =========================
   STATUS BADGE
========================= */

.status-badge {

  display: inline-flex;

  align-items: center;

  gap: 6px;

  padding:
    8px 12px;

  border-radius: 999px;

  font-size: 9px;

  font-weight: 600;

  white-space: nowrap;
}


.status-pending {

  color: #956800;

  background: #fff6d9;
}


.status-ready {

  color: #166d86;

  background: #e9f7fa;
}


.status-claimed {

  color: #28754e;

  background: #e9f6ee;
}


.status-dot {

  width: 6px;
  height: 6px;

  border-radius: 50%;

  background:
    currentColor;
}


/* =========================
   DETAILS
========================= */

.details-grid {

  margin-top: 18px;

  display: grid;

  grid-template-columns:
    1fr 1fr;

  gap: 12px;
}


.detail-card {

  padding: 14px;

  box-sizing: border-box;

  border:
    1px solid #e0e7ee;

  border-radius: 10px;

  background: #f8fafc;
}


.detail-card span {

  display: block;

  margin-bottom: 6px;

  color: #61758f;

  font-size: 9px;
}


.detail-card strong {

  color: #182238;

  font-size: 12px;

  font-weight: 700;
}


/* =========================
   STATUS TRACKER
========================= */

.status-tracker {

  margin-top: 36px;

  display: flex;

  align-items: center;
}


.tracker-step {

  display: flex;

  align-items: center;

  gap: 7px;

  color: #7c8ca0;

  font-size: 10px;

  white-space: nowrap;
}


.tracker-step.active {

  color: #227d9a;
}


.step-circle {

  width: 23px;
  height: 23px;

  display: flex;

  align-items: center;

  justify-content: center;

  box-sizing: border-box;

  border:
    1px solid #d5dfe8;

  border-radius: 50%;

  background: #ffffff;

  color: #ffffff;
}


.step-circle span {

  width: 6px;
  height: 6px;

  border-radius: 50%;

  background: #9caabd;
}


.tracker-step.active
.step-circle {

  border-color: #25809e;

  background: #25809e;
}


.tracker-step.active
.step-circle span {

  background: #ffffff;
}


.step-circle svg {

  width: 12px;
  height: 12px;
}


.tracker-line {

  flex: 1;

  height: 2px;

  margin:
    0 16px;

  background: #dce4eb;
}


.tracker-line.active {

  background: #5aaec5;
}


.step-label {

  font-weight: 500;
}


.status-message {

  margin: 13px 0 0;

  color: #6b7d95;

  font-size: 9px;
}


/* =========================
   FEEDBACK BUTTON
========================= */

.feedback-action {

  margin-top: 21px;

  display: flex;

  justify-content: center;
}


.feedback-submitted-button {
  background: #42a878;
  cursor: not-allowed;
  opacity: 0.9;
}


.feedback-button {

  min-width: 145px;

  padding:
    11px 20px;

  border: none;

  border-radius: 8px;

  background: #f5a300;

  color: #ffffff;

  font-family: inherit;

  font-size: 10px;

  font-weight: 600;

  cursor: pointer;
}


.feedback-button span {

  margin-right: 4px;
}


/* =========================
   NOT FOUND
========================= */

.not-found-card {

  width:
    min(
      570px,
      100%
    );

  margin:
    25px auto 0;

  padding:
    32px 25px;

  box-sizing: border-box;

  text-align: center;

  background: #ffffff;

  border-radius: 14px;

  box-shadow:
    0 8px 24px
    rgba(0,0,0,0.12);
}


.not-found-icon {

  width: 44px;
  height: 44px;

  margin:
    0 auto 13px;

  display: flex;

  align-items: center;

  justify-content: center;

  border-radius: 50%;

  background: #edf6f8;

  color: #237f9d;
}


.not-found-icon svg {

  width: 21px;
  height: 21px;
}


.not-found-card h2 {

  margin: 0;

  color: #182238;

  font-size: 16px;
}


.not-found-card p {

  max-width: 390px;

  margin:
    8px auto 18px;

  color: #718198;

  font-size: 10px;

  line-height: 1.6;
}


.try-again-button {

  padding:
    9px 17px;

  border:
    1px solid #d1dce6;

  border-radius: 8px;

  background: #ffffff;

  color: #247d99;

  font-family: inherit;

  font-size: 10px;

  font-weight: 600;

  cursor: pointer;
}


/* =========================
   MODAL
========================= */

.modal-overlay {

  position: fixed;

  inset: 0;

  z-index: 100;

  display: flex;

  align-items: center;

  justify-content: center;

  padding: 20px;

  box-sizing: border-box;

  background:
    rgba(18,31,44,0.58);
}


.feedback-modal,
.success-modal {

  position: relative;

  width:
    min(
      890px,
      100%
    );

  max-height:
    calc(100vh - 40px);

  overflow: auto;

  box-sizing: border-box;

  background: #ffffff;

  border-radius: 13px;

  box-shadow:
    0 20px 50px
    rgba(0,0,0,0.2);
}


.feedback-modal {

  padding:
    23px 20px 19px;
}


.success-modal {

  width:
    min(
      400px,
      100%
    );

  padding: 28px;

  text-align: center;
}


.modal-close {

  position: absolute;

  top: 10px;
  right: 13px;

  border: none;

  background: transparent;

  color: #7d8da1;

  font-family: inherit;

  font-size: 24px;

  line-height: 1;

  cursor: pointer;
}


/* =========================
   FEEDBACK FORM
========================= */

.feedback-modal {
  width:
    min(
      910px,
      100%
    );

  max-height:
    calc(100vh - 60px);

  padding:
    22px 20px 20px;

  overflow: auto;

  box-sizing: border-box;

  background: #ffffff;

  border-radius: 13px;

  box-shadow:
    0 20px 50px
    rgba(0,0,0,0.2);

  text-align: left;
}


.feedback-heading {
  margin: 0;

  text-align: center;
}


.feedback-heading h2 {
  margin: 0;

  color: #182238;

  font-size: 16px;

  font-weight: 700;
}


.feedback-heading p {
  margin:
    5px 0 0;

  color: #718198;

  font-size: 10px;
}


/* =========================
   FORM ROWS
========================= */

.feedback-row {
  margin-top: 19px;

  display: grid;

  grid-template-columns:
    95px 1fr;

  gap: 0;

  align-items: center;
}


.feedback-row label {
  color: #182238;

  font-size: 10px;

  font-weight: 500;
}


/* =========================
   RATING
========================= */

.rating-row {
  margin-top: 20px;
}


.rating-row .stars {
  display: flex;

  align-items: center;

  justify-content: flex-start;

  gap: 8px;
}


.star-button {
  width: 28px;
  height: 28px;

  padding: 0;

  display: flex;

  align-items: center;
  justify-content: center;

  border: none;

  border-radius: 50%;

  background: #ffffff;

  color: #8ea0b3;

  font-size: 15px;

  cursor: pointer;
}


.star-button.selected {
  background: #237f9d;

  color: #ffffff;
}


/* =========================
   TYPE
========================= */

.type-row {
  margin-top: 16px;
}


.compact-select {
  width: 138px;
}


.select-wrapper {
  position: relative;
}


.select-wrapper select {
  width: 100%;

  height: 43px;

  padding:
    0 34px 0 12px;

  appearance: none;

  box-sizing: border-box;

  border:
    1px solid #d9e1e9;

  border-radius: 8px;

  outline: none;

  background: #ffffff;

  color: #344054;

  font-family: inherit;

  font-size: 10px;

  cursor: pointer;
}


.select-wrapper select:focus {
  border-color: #2699b9;

  box-shadow:
    0 0 0 2px
    rgba(38,153,185,0.10);
}


.select-icon {
  position: absolute;

  right: 11px;

  top: 50%;

  width: 13px;
  height: 13px;

  transform:
    translateY(-50%);

  color: #8ca0b4;

  pointer-events: none;
}


/* =========================
   COMMENT
========================= */

.comment-row {
  margin-top: 16px;

  align-items: start;
}


.comment-row label {
  padding-top: 9px;
}


.comment-row textarea {
  width: 100%;

  min-height: 120px;

  padding:
    12px;

  box-sizing: border-box;

  resize: vertical;

  border:
    1px solid #d9e1e9;

  border-radius: 8px;

  outline: none;

  color: #344054;

  background: #ffffff;

  font-family: inherit;

  font-size: 10px;
}


.comment-row textarea:focus {
  border-color: #2699b9;

  box-shadow:
    0 0 0 2px
    rgba(38,153,185,0.10);
}


.comment-row textarea::placeholder {
  color: #9aa9bd;
}


/* =========================
   ERROR
========================= */

.feedback-error {
  margin:
    10px 0 0 95px;

  color: #dc2626;

  font-size: 9px;
}


/* =========================
   FOOTER
========================= */

.feedback-footer {
  margin-top: 16px;

  padding-top: 0;

  display: flex;

  align-items: center;

  justify-content: space-between;

  gap: 15px;
}


.feedback-footer p {
  margin: 0;

  color: #718198;

  font-size: 9px;

  line-height: 1.4;
}


.feedback-actions {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 8px;
}


.cancel-button,
.submit-button {
  height: 42px;

  padding:
    0 15px;

  border-radius: 8px;

  font-family: inherit;

  font-size: 10px;

  font-weight: 600;

  cursor: pointer;
}


.cancel-button {
  border:
    1px solid #d3dce6;

  background: #ffffff;

  color: #61758f;
}


.submit-button {
  min-width: 80px;

  border: none;

  background:
    linear-gradient(
      135deg,
      #2087a6,
      #269dbd
    );

  color: #ffffff;
}


.cancel-button:disabled,
.submit-button:disabled {
  opacity: 0.6;

  cursor: not-allowed;
}


/* =========================
   SUCCESS
========================= */

.success-icon {

  width: 54px;
  height: 54px;

  margin:
    0 auto 15px;

  display: flex;

  align-items: center;

  justify-content: center;

  border-radius: 50%;

  background: #e9f7ef;

  color: #2e8c5a;
}


.success-icon svg {

  width: 25px;
  height: 25px;
}


.success-modal h2 {

  margin: 0;

  color: #182238;

  font-size: 16px;
}


.success-modal p {

  margin:
    8px 0 0;

  color: #718198;

  font-size: 10px;

  line-height: 1.6;
}


.success-modal .modal-button {

  margin-top: 18px;
}


/* =========================
   ANIMATION
========================= */

@keyframes spin {

  to {
    transform: rotate(360deg);
  }

}


/* =========================
   RESPONSIVE
========================= */

@media (max-width: 720px) {

  .customer-content {

    width:
      calc(100% - 28px);
  }


  .tracker-section {

    padding-top: 60px;
  }


  .hero-logo {

    width: 260px;

    max-width: 80vw;
  }


  .search-card {

    flex-direction: column;
  }


  .search-input-wrapper,
  .track-button {

    width: 100%;
  }


  .order-heading {

    flex-direction: column;
  }


  .status-badge {

    align-self: flex-start;
  }


  .details-grid {

    grid-template-columns:
      1fr;
  }


  .status-tracker {

    overflow-x: auto;

    padding-bottom: 4px;
  }


  .tracker-line {

    min-width: 35px;
  }


  .feedback-row {
  grid-template-columns: 1fr;

  gap: 8px;
}


.feedback-row label {
  padding-top: 0;
}


.rating-row .stars {
  justify-content: flex-start;
}


.compact-select {
  width: 100%;
}


.comment-row label {
  padding-top: 0;
}


.feedback-error {
  margin-left: 0;
}


.feedback-footer {
  flex-direction: column;

  align-items: stretch;
}


.feedback-actions {
  justify-content: flex-end;
}

}

</style>