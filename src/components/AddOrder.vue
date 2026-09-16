<template>
  <div class="app-layout">

    <!-- =========================
         SIDEBAR
    ========================== -->
    <aside class="sidebar">

      <div class="sidebar-logo">
        <img
          src="../assets/droplet.png"
          alt="WashUp"
          class="logo-droplet"
        />

        <img
          src="../assets/washupblue.png"
          alt="WashUp"
          class="logo-text"
        />
      </div>

      <nav class="sidebar-nav">

        <button
          type="button"
          class="nav-item"
          @click="goTo('Dashboard')"
        >
          <img
            src="../assets/icondashboardblue.png"
            alt=""
          />
          <span>Dashboard</span>
        </button>

        <button
          type="button"
          class="nav-item active"
          @click="goTo('Orders')"
        >
          <img
            src="../assets/iconordergray.png"
            alt=""
          />
          <span>Orders</span>
        </button>

        <button
          type="button"
          class="nav-item"
          @click="goTo('Services & Add-ons')"
        >
          <img
            src="../assets/iconservicesgray.png"
            alt=""
          />
          <span>Services &amp; Add-ons</span>
        </button>

        <button
          type="button"
          class="nav-item"
          @click="goTo('Reports')"
        >
          <img
            src="../assets/iconreportsgray.png"
            alt=""
          />
          <span>Reports</span>
        </button>

      </nav>

      <div class="sidebar-bottom">

        <button
          type="button"
          class="nav-item logout"
          @click="handleLogout"
        >
          <img
            src="../assets/iconlogoutgray.png"
            alt=""
          />
          <span>Logout</span>
        </button>

      </div>

    </aside>


    <!-- =========================
         MAIN AREA
    ========================== -->
    <main class="main-area">

      <!-- TOP HEADER -->
      <header class="top-header">

        <div class="date">
          Sep 17, 2026 · Thursday
        </div>

        <div class="user-info">

          <div class="user-details">
            <strong>Admin</strong>
            <span>Shop Staff</span>
          </div>

          <div class="user-avatar">
            A
          </div>

        </div>

      </header>


      <!-- =========================
           SCROLLABLE CONTENT
      ========================== -->
      <div class="content-container">

        <!-- PAGE TITLE -->
        <div class="page-heading">

          <h1>Add New Order</h1>

          <p>
            Create a new laundry order with service, add-ons,
            and pickup details.
          </p>

        </div>


        <!-- =========================
             ORDER NUMBER
        ========================== -->
        <section class="order-number-card">

          <div>

            <span class="field-label">
              Order Number
            </span>

            <div class="order-number">
              {{ orderNumber }}
            </div>

          </div>

          <span class="auto-badge">
            Auto-generated
          </span>

        </section>


        <!-- =========================
             ORDER DETAILS
        ========================== -->
        <section class="form-card">

          <div class="section-title">

            <h2>Order Details</h2>

            <p>
              Enter the customer details and select the base service.
            </p>

          </div>


          <div class="form-grid">

            <!-- CUSTOMER -->
            <div class="form-group">

              <label>
                Customer Name
              </label>

              <input
                v-model="form.customerName"
                type="text"
                placeholder="e.g. Maria Santos"
                :class="{ error: errors.customerName }"
              />

              <span
                v-if="errors.customerName"
                class="error-message"
              >
                {{ errors.customerName }}
              </span>

            </div>


            <!-- PHONE -->
            <div class="form-group">

              <label>
                Phone Number
              </label>

              <input
                v-model="form.phone"
                type="tel"
                placeholder="e.g. 0917 555 0123"
                :class="{ error: errors.phone }"
              />

              <span
                v-if="errors.phone"
                class="error-message"
              >
                {{ errors.phone }}
              </span>

            </div>


            <!-- SERVICE -->
            <div class="form-group">

              <label>
                Service
              </label>

              <select
                v-model="form.service"
                :class="{ error: errors.service }"
              >

                <option value="">
                  Select service
                </option>

                <option value="Wash Only">
                  Wash Only - ₱25/kg
                </option>

                <option value="Wash & Dry">
                  Wash &amp; Dry - ₱50/kg
                </option>

              </select>

              <span
                v-if="errors.service"
                class="error-message"
              >
                {{ errors.service }}
              </span>

            </div>


            <!-- WEIGHT -->
            <div class="form-group">

              <label>
                Weight (kg)
              </label>

              <div class="input-with-unit">

                <input
                  v-model.number="form.weight"
                  type="number"
                  min="0"
                  step="0.1"
                  placeholder="0"
                  :class="{ error: errors.weight }"
                />

                <span>kg</span>

              </div>

              <span
                v-if="errors.weight"
                class="error-message"
              >
                {{ errors.weight }}
              </span>

            </div>

          </div>

        </section>


        <!-- =========================
             ADD-ONS
        ========================== -->
        <section class="form-card">

          <div class="section-title">

            <h2>Add-ons</h2>

            <p>
              Optional extras for this order.
            </p>

          </div>


          <!-- FABRIC CONDITIONER -->
          <div class="addon-row">

            <label class="addon-left">

              <input
                v-model="addons.fabricConditioner.selected"
                type="checkbox"
              />

              <span class="custom-checkbox"></span>

              <span class="addon-info">

                <strong>
                  Fabric Conditioner
                </strong>

                <small>
                  Softens fabric and leaves a fresh scent
                </small>

              </span>

            </label>


            <div class="addon-right">

              <div class="quantity-control">

                <button
                  type="button"
                  @click="decreaseAddon('fabricConditioner')"
                  :disabled="!addons.fabricConditioner.selected"
                >
                  −
                </button>

                <span>
                  {{ addons.fabricConditioner.quantity }}
                </span>

                <button
                  type="button"
                  @click="increaseAddon('fabricConditioner')"
                  :disabled="!addons.fabricConditioner.selected"
                >
                  +
                </button>

              </div>

              <strong class="addon-price">
                ₱15.00 each
              </strong>

            </div>

          </div>


          <!-- EXTRA DRYING -->
          <div class="addon-row">

            <label class="addon-left">

              <input
                v-model="addons.extraDrying.selected"
                type="checkbox"
              />

              <span class="custom-checkbox"></span>

              <span class="addon-info">

                <strong>
                  Extra Drying
                </strong>

                <small>
                  Additional drying time for thicker or bulkier items
                </small>

              </span>

            </label>


            <div class="addon-right">

              <div class="quantity-control">

                <button
                  type="button"
                  @click="decreaseAddon('extraDrying')"
                  :disabled="!addons.extraDrying.selected"
                >
                  −
                </button>

                <span>
                  {{ addons.extraDrying.quantity }}
                </span>

                <button
                  type="button"
                  @click="increaseAddon('extraDrying')"
                  :disabled="!addons.extraDrying.selected"
                >
                  +
                </button>

              </div>

              <strong class="addon-price">
                ₱20.00 each
              </strong>

            </div>

          </div>

        </section>


        <!-- =========================
             ESTIMATED PICKUP
        ========================== -->
        <section class="form-card">

          <div class="section-title">

            <h2>Estimated Pickup</h2>

            <p>
              Choose the expected pickup date and time.
            </p>

          </div>


          <div class="form-grid">

            <div class="form-group">

              <label>
                Pickup Date
              </label>

              <input
                v-model="form.pickupDate"
                type="date"
                :class="{ error: errors.pickupDate }"
              />

              <span
                v-if="errors.pickupDate"
                class="error-message"
              >
                {{ errors.pickupDate }}
              </span>

            </div>


            <div class="form-group">

              <label>
                Pickup Time
              </label>

              <input
                v-model="form.pickupTime"
                type="time"
                :class="{ error: errors.pickupTime }"
              />

              <span
                v-if="errors.pickupTime"
                class="error-message"
              >
                {{ errors.pickupTime }}
              </span>

            </div>

          </div>

        </section>


        <!-- =========================
             PRICE BREAKDOWN
        ========================== -->
        <section class="form-card price-card">

          <div class="section-title">

            <h2>
              Price Breakdown
            </h2>

            <p>
              Auto-calculated total based on the selected service and add-ons.
            </p>

          </div>


          <div class="price-lines">

            <div class="price-line">

              <span>
                Base Service -
                {{ form.service || 'No service' }}
                {{ form.weight > 0 ? `${form.weight}kg` : '' }}
              </span>

              <strong>
                ₱{{ formatPrice(baseServiceCost) }}
              </strong>

            </div>


            <div
              v-if="addons.fabricConditioner.selected &&
                     addons.fabricConditioner.quantity > 0"
              class="price-line"
            >

              <span>
                Fabric Conditioner x{{ addons.fabricConditioner.quantity }}
              </span>

              <strong>
                ₱{{ formatPrice(fabricConditionerCost) }}
              </strong>

            </div>


            <div
              v-if="addons.extraDrying.selected &&
                     addons.extraDrying.quantity > 0"
              class="price-line"
            >

              <span>
                Extra Drying x{{ addons.extraDrying.quantity }}
              </span>

              <strong>
                ₱{{ formatPrice(extraDryingCost) }}
              </strong>

            </div>

          </div>


          <div class="price-total">

            <span>
              Total
            </span>

            <strong>
              ₱{{ formatPrice(totalPrice) }}
            </strong>

          </div>

        </section>


        <!-- =========================
             ACTIONS
        ========================== -->
        <div class="form-actions">

          <button
            type="button"
            class="cancel-button"
            @click="goTo('Orders')"
          >
            Cancel
          </button>

          <button
            type="button"
            class="create-button"
            @click="createOrder"
          >
            Create Order
          </button>

        </div>

      </div>

    </main>


    <!-- =========================
         RECEIPT MODAL
    ========================== -->
    <div
      v-if="showReceipt"
      class="modal-overlay"
    >

      <div class="receipt-modal">

        <button
          type="button"
          class="close-modal"
          @click="showReceipt = false"
        >
          ×
        </button>


        <div class="receipt-header">

          <div class="receipt-logo">
            <img
              src="../assets/droplet.png"
              alt="WashUp"
            />

            <img
              src="../assets/washupblue.png"
              alt="WashUp"
            />
          </div>

          <p>
            Laundry Shop Management System
          </p>

        </div>


        <div class="receipt-divider"></div>


        <div class="receipt-order-number">
          {{ orderNumber }}
        </div>


        <div class="receipt-details">

          <div>
            <span>Customer</span>
            <strong>{{ form.customerName }}</strong>
          </div>

          <div>
            <span>Phone</span>
            <strong>{{ form.phone }}</strong>
          </div>

          <div>
            <span>Service</span>
            <strong>{{ form.service }}</strong>
          </div>

          <div>
            <span>Weight</span>
            <strong>{{ form.weight }} kg</strong>
          </div>

          <div>
            <span>Estimated Pickup</span>
            <strong>
              {{ formattedPickup }}
            </strong>
          </div>

          <div>
            <span>Status</span>
            <strong class="receipt-status">
              Pending
            </strong>
          </div>

        </div>


        <div class="receipt-divider"></div>


        <div class="receipt-items">

          <div>
            <span>
              Base Service
            </span>

            <strong>
              ₱{{ formatPrice(baseServiceCost) }}
            </strong>
          </div>

          <div
            v-if="fabricConditionerCost > 0"
          >
            <span>
              Fabric Conditioner ×
              {{ addons.fabricConditioner.quantity }}
            </span>

            <strong>
              ₱{{ formatPrice(fabricConditionerCost) }}
            </strong>
          </div>

          <div
            v-if="extraDryingCost > 0"
          >
            <span>
              Extra Drying ×
              {{ addons.extraDrying.quantity }}
            </span>

            <strong>
              ₱{{ formatPrice(extraDryingCost) }}
            </strong>
          </div>

        </div>


        <div class="receipt-total">

          <span>
            Total
          </span>

          <strong>
            ₱{{ formatPrice(totalPrice) }}
          </strong>

        </div>


        <div class="receipt-actions">

          <button
            type="button"
            class="receipt-close"
            @click="showReceipt = false"
          >
            Close
          </button>

          <button
            type="button"
            class="receipt-orders"
            @click="goTo('Orders')"
          >
            Back to Orders
          </button>

        </div>

      </div>

    </div>

  </div>
</template>


<script setup>
import { computed, reactive, ref } from 'vue'

const emit = defineEmits([
  'navigate',
  'logout'
])


/* =========================
   ORDER NUMBER
========================= */

const orderNumber = ref('WU-0130')


/* =========================
   FORM
========================= */

const form = reactive({
  customerName: '',
  phone: '',
  service: 'Wash & Dry',
  weight: 5,
  pickupDate: '2026-05-18',
  pickupTime: '10:00'
})


/* =========================
   ADD-ONS
========================= */

const addons = reactive({

  fabricConditioner: {
    selected: true,
    quantity: 1,
    price: 15
  },

  extraDrying: {
    selected: true,
    quantity: 1,
    price: 20
  }

})


/* =========================
   ERRORS
========================= */

const errors = reactive({

  customerName: '',
  phone: '',
  service: '',
  weight: '',
  pickupDate: '',
  pickupTime: ''

})


/* =========================
   RECEIPT
========================= */

const showReceipt = ref(false)


/* =========================
   SERVICE PRICE
========================= */

const servicePrice = computed(() => {

  if (form.service === 'Wash Only') {
    return 25
  }

  if (form.service === 'Wash & Dry') {
    return 50
  }

  return 0
})


/* =========================
   BASE SERVICE COST
========================= */

const baseServiceCost = computed(() => {

  const weight = Number(form.weight) || 0

  return servicePrice.value * weight
})


/* =========================
   ADD-ON COSTS
========================= */

const fabricConditionerCost = computed(() => {

  if (!addons.fabricConditioner.selected) {
    return 0
  }

  return (
    addons.fabricConditioner.quantity *
    addons.fabricConditioner.price
  )
})


const extraDryingCost = computed(() => {

  if (!addons.extraDrying.selected) {
    return 0
  }

  return (
    addons.extraDrying.quantity *
    addons.extraDrying.price
  )
})


/* =========================
   TOTAL
========================= */

const totalPrice = computed(() => {

  return (
    baseServiceCost.value +
    fabricConditionerCost.value +
    extraDryingCost.value
  )

})


/* =========================
   PICKUP DISPLAY
========================= */

const formattedPickup = computed(() => {

  if (!form.pickupDate || !form.pickupTime) {
    return 'Not set'
  }

  const date = new Date(
    `${form.pickupDate}T${form.pickupTime}`
  )

  if (Number.isNaN(date.getTime())) {
    return `${form.pickupDate} ${form.pickupTime}`
  }

  return date.toLocaleString(
    'en-US',
    {
      month: 'short',
      day: 'numeric',
      year: 'numeric',
      hour: 'numeric',
      minute: '2-digit'
    }
  )

})


/* =========================
   FORMAT PRICE
========================= */

const formatPrice = (value) => {

  return Number(value || 0).toFixed(2)

}


/* =========================
   ADD-ON QUANTITY
========================= */

const increaseAddon = (addonName) => {

  addons[addonName].selected = true

  addons[addonName].quantity += 1

}


const decreaseAddon = (addonName) => {

  if (addons[addonName].quantity > 1) {

    addons[addonName].quantity -= 1

  } else {

    addons[addonName].quantity = 0
    addons[addonName].selected = false

  }

}


/* =========================
   VALIDATION
========================= */

const validateForm = () => {

  errors.customerName = ''
  errors.phone = ''
  errors.service = ''
  errors.weight = ''
  errors.pickupDate = ''
  errors.pickupTime = ''

  let valid = true


  if (!form.customerName.trim()) {

    errors.customerName =
      'Customer name is required.'

    valid = false

  }


  if (!form.phone.trim()) {

    errors.phone =
      'Phone number is required.'

    valid = false

  }


  if (!form.service) {

    errors.service =
      'Please select a service.'

    valid = false

  }


  if (!form.weight || Number(form.weight) <= 0) {

    errors.weight =
      'Weight must be greater than 0.'

    valid = false

  }


  if (!form.pickupDate) {

    errors.pickupDate =
      'Pickup date is required.'

    valid = false

  }


  if (!form.pickupTime) {

    errors.pickupTime =
      'Pickup time is required.'

    valid = false

  }


  return valid

}


/* =========================
   CREATE ORDER
========================= */

const createOrder = () => {

  if (!validateForm()) {
    return
  }

  showReceipt.value = true

}


/* =========================
   NAVIGATION
========================= */

const goTo = (page) => {

  emit('navigate', page)

}


/* =========================
   LOGOUT
========================= */

const handleLogout = async () => {

  const token =
    localStorage.getItem('washup_token')

  try {

    if (token) {

      await fetch('/api/logout', {
        method: 'POST',

        headers: {
          Accept: 'application/json',

          Authorization:
            `Bearer ${token}`
        }
      })

    }

  } catch (error) {

    console.error(
      'Logout error:',
      error
    )

  } finally {

    localStorage.removeItem(
      'washup_token'
    )

    localStorage.removeItem(
      'washup_user'
    )

    emit('logout')

  }

}
</script>


<style scoped>

* {
  box-sizing: border-box;
}


/* =========================
   LAYOUT
========================= */

.app-layout {
  min-height: 100vh;
  width: 100%;

  background: #f7f9fb;

  color: #172033;
}


/* =========================
   SIDEBAR
========================= */

.sidebar {
  position: fixed;

  top: 0;
  left: 0;
  bottom: 0;

  width: 218px;

  display: flex;
  flex-direction: column;

  background: #ffffff;

  border-right: 1px solid #e4e9ef;

  z-index: 20;
}


.sidebar-logo {
  height: 84px;

  display: flex;
  align-items: center;

  padding: 0 22px;

  border-bottom: 1px solid #e4e9ef;
}


.logo-droplet {
  width: 35px;
  height: 35px;

  object-fit: contain;

  margin-right: 7px;
}


.logo-text {
  width: 108px;
  height: auto;

  object-fit: contain;
}


/* =========================
   SIDEBAR NAV
========================= */

.sidebar-nav {
  padding: 20px 10px;
}


.nav-item {
  width: 100%;
  height: 40px;

  display: flex;
  align-items: center;

  padding: 0 12px;

  margin-bottom: 4px;

  border: none;
  border-radius: 7px;

  background: transparent;

  color: #52627a;

  font-family: inherit;
  font-size: 12px;

  text-align: left;

  cursor: pointer;

  transition:
    background 0.15s ease,
    color 0.15s ease;
}


.nav-item img {
  width: 15px;
  height: 15px;

  object-fit: contain;

  margin-right: 11px;
}


.nav-item:hover {
  background: #f1f7f9;
}


.nav-item.active {
  background: #edf8fa;

  color: #172c47;

  font-weight: 500;
}


.nav-item.active img {
  opacity: 1;
}


.sidebar-bottom {
  margin-top: auto;

  padding: 15px 10px 20px;

  border-top: 1px solid #e4e9ef;
}


.nav-item.logout {
  margin: 0;
}


/* =========================
   MAIN
========================= */

.main-area {
  width: calc(100% - 218px);

  min-height: 100vh;

  margin-left: 218px;
}


/* =========================
   TOP HEADER
========================= */

.top-header {
  height: 84px;

  display: flex;
  align-items: center;
  justify-content: space-between;

  padding: 0 32px;

  background: #ffffff;

  border-bottom: 1px solid #e4e9ef;
}


.date {
  font-size: 10px;

  color: #64748b;
}


.user-info {
  display: flex;
  align-items: center;

  gap: 10px;
}


.user-details {
  display: flex;
  flex-direction: column;

  align-items: flex-end;
}


.user-details strong {
  font-size: 11px;

  color: #172033;
}


.user-details span {
  margin-top: 2px;

  font-size: 9px;

  color: #8090a5;
}


.user-avatar {
  width: 28px;
  height: 28px;

  display: flex;
  align-items: center;
  justify-content: center;

  border-radius: 50%;

  background: #dff3f7;

  color: #217f98;

  font-size: 11px;
}


/* =========================
   CONTENT
========================= */

.content-container {
  width: 100%;

  max-width: 1050px;

  margin: 0 auto;

  padding: 27px 25px 40px;
}


/* =========================
   PAGE HEADING
========================= */

.page-heading {
  margin-bottom: 18px;
}


.page-heading h1 {
  margin: 0;

  font-size: 19px;

  font-weight: 700;

  color: #172033;
}


.page-heading p {
  margin: 5px 0 0;

  font-size: 9px;

  color: #7f91a8;
}


/* =========================
   ORDER NUMBER
========================= */

.order-number-card {
  min-height: 65px;

  display: flex;
  align-items: center;
  justify-content: space-between;

  padding: 14px 16px;

  margin-bottom: 18px;

  background: #ffffff;

  border: 1px solid #e4e9ef;

  border-radius: 9px;

  box-shadow:
    0 1px 2px rgba(16, 24, 40, 0.02);
}


.field-label {
  display: block;

  margin-bottom: 7px;

  font-size: 9px;

  color: #64748b;
}


.order-number {
  font-size: 14px;

  font-weight: 700;

  color: #172033;
}


.auto-badge {
  padding: 5px 9px;

  border-radius: 20px;

  background: #edf8fa;

  color: #217f98;

  font-size: 8px;

  font-weight: 600;
}


/* =========================
   FORM CARDS
========================= */

.form-card {
  margin-bottom: 18px;

  padding: 17px 16px;

  background: #ffffff;

  border: 1px solid #e4e9ef;

  border-radius: 9px;

  box-shadow:
    0 1px 2px rgba(16, 24, 40, 0.02);
}


.section-title {
  margin-bottom: 16px;
}


.section-title h2 {
  margin: 0;

  font-size: 12px;

  font-weight: 700;

  color: #172033;
}


.section-title p {
  margin: 4px 0 0;

  font-size: 8px;

  color: #7f91a8;
}


/* =========================
   FORM GRID
========================= */

.form-grid {
  display: grid;

  grid-template-columns:
    repeat(2, minmax(0, 1fr));

  gap: 14px 12px;
}


.form-group {
  min-width: 0;
}


.form-group label {
  display: block;

  margin-bottom: 6px;

  font-size: 9px;

  font-weight: 600;

  color: #52627a;
}


.form-group input,
.form-group select {
  width: 100%;

  height: 34px;

  padding: 0 10px;

  border: 1px solid #dce4ec;

  border-radius: 6px;

  background: #ffffff;

  color: #52627a;

  font-family: inherit;

  font-size: 9px;

  outline: none;

  transition:
    border-color 0.15s ease,
    box-shadow 0.15s ease;
}


.form-group input:focus,
.form-group select:focus {
  border-color: #8fc6d3;

  box-shadow:
    0 0 0 2px rgba(32, 133, 160, 0.08);
}


.form-group input::placeholder {
  color: #a0adbd;
}


.form-group input.error,
.form-group select.error {
  border-color: #df7777;
}


.error-message {
  display: block;

  margin-top: 4px;

  font-size: 8px;

  color: #c95f5f;
}


.input-with-unit {
  position: relative;
}


.input-with-unit input {
  padding-right: 35px;
}


.input-with-unit span {
  position: absolute;

  top: 50%;
  right: 10px;

  transform: translateY(-50%);

  font-size: 8px;

  color: #8c9bae;
}


/* =========================
   ADD-ONS
========================= */

.addon-row {
  min-height: 55px;

  display: flex;
  align-items: center;
  justify-content: space-between;

  padding: 9px 10px;

  margin-bottom: 9px;

  background: #f8fafc;

  border-radius: 7px;
}


.addon-row:last-child {
  margin-bottom: 0;
}


.addon-left {
  display: flex;
  align-items: center;

  cursor: pointer;
}


.addon-left input {
  position: absolute;

  opacity: 0;

  pointer-events: none;
}


.custom-checkbox {
  width: 14px;
  height: 14px;

  flex-shrink: 0;

  display: flex;
  align-items: center;
  justify-content: center;

  margin-right: 10px;

  border: 1px solid #c9d5df;

  border-radius: 4px;

  background: #ffffff;
}


.addon-left input:checked + .custom-checkbox {
  background: #2186a0;

  border-color: #2186a0;
}


.addon-left input:checked + .custom-checkbox::after {
  content: '✓';

  color: #ffffff;

  font-size: 9px;

  font-weight: 700;
}


.addon-info {
  display: flex;
  flex-direction: column;
}


.addon-info strong {
  font-size: 9px;

  color: #172033;
}


.addon-info small {
  margin-top: 3px;

  font-size: 7px;

  color: #8a99ac;
}


.addon-right {
  display: flex;
  align-items: center;

  gap: 10px;
}


.quantity-control {
  height: 25px;

  display: flex;
  align-items: center;

  border: 1px solid #dce4ec;

  border-radius: 6px;

  background: #ffffff;

  overflow: hidden;
}


.quantity-control button {
  width: 25px;
  height: 25px;

  border: none;

  background: #ffffff;

  color: #53667e;

  font-size: 12px;

  cursor: pointer;
}


.quantity-control button:hover:not(:disabled) {
  background: #f1f7f9;
}


.quantity-control button:disabled {
  color: #c4ccd5;

  cursor: not-allowed;
}


.quantity-control span {
  min-width: 25px;

  text-align: center;

  font-size: 9px;

  color: #172033;
}


.addon-price {
  min-width: 65px;

  font-size: 8px;

  color: #172033;

  text-align: right;
}


/* =========================
   PRICE BREAKDOWN
========================= */

.price-card {
  padding-bottom: 12px;
}


.price-lines {
  padding-bottom: 11px;

  border-bottom: 1px solid #e4e9ef;
}


.price-line {
  display: flex;
  align-items: center;
  justify-content: space-between;

  min-height: 27px;

  font-size: 8px;

  color: #52627a;
}


.price-line strong {
  color: #172033;

  font-weight: 600;
}


.price-total {
  display: flex;
  align-items: center;
  justify-content: space-between;

  padding-top: 13px;
}


.price-total span {
  font-size: 10px;

  font-weight: 700;

  color: #172033;
}


.price-total strong {
  font-size: 18px;

  font-weight: 700;

  color: #2186a0;
}


/* =========================
   ACTIONS
========================= */

.form-actions {
  display: flex;
  align-items: center;
  justify-content: space-between;

  margin-top: 2px;

  padding-bottom: 15px;
}


.cancel-button,
.create-button {
  height: 34px;

  padding: 0 20px;

  border-radius: 7px;

  font-family: inherit;

  font-size: 9px;

  font-weight: 600;

  cursor: pointer;
}


.cancel-button {
  border: 1px solid #dce4ec;

  background: #ffffff;

  color: #52627a;
}


.cancel-button:hover {
  background: #f7f9fb;
}


.create-button {
  border: none;

  background: #2085a0;

  color: #ffffff;

  box-shadow:
    0 3px 8px rgba(32, 133, 160, 0.15);
}


.create-button:hover {
  background: #19768f;
}


/* =========================
   RECEIPT MODAL
========================= */

.modal-overlay {
  position: fixed;

  inset: 0;

  z-index: 100;

  display: flex;
  align-items: center;
  justify-content: center;

  padding: 20px;

  background: rgba(23, 32, 51, 0.45);
}


.receipt-modal {
  position: relative;

  width: 100%;

  max-width: 420px;

  max-height: 90vh;

  overflow-y: auto;

  padding: 25px;

  background: #ffffff;

  border-radius: 12px;

  box-shadow:
    0 20px 50px rgba(16, 24, 40, 0.2);
}


.close-modal {
  position: absolute;

  top: 12px;
  right: 15px;

  width: 28px;
  height: 28px;

  border: none;

  background: transparent;

  color: #7f91a8;

  font-size: 20px;

  cursor: pointer;
}


.receipt-header {
  text-align: center;
}


.receipt-logo {
  display: flex;
  align-items: center;
  justify-content: center;
}


.receipt-logo img:first-child {
  width: 28px;
  height: 28px;

  object-fit: contain;

  margin-right: 5px;
}


.receipt-logo img:last-child {
  width: 95px;

  height: auto;

  object-fit: contain;
}


.receipt-header p {
  margin: 7px 0 0;

  font-size: 8px;

  color: #8090a5;
}


.receipt-divider {
  height: 1px;

  margin: 18px 0;

  background: #e4e9ef;
}


.receipt-order-number {
  margin-bottom: 15px;

  text-align: center;

  font-size: 15px;

  font-weight: 700;

  color: #172033;
}


.receipt-details > div,
.receipt-items > div {
  display: flex;

  align-items: center;
  justify-content: space-between;

  min-height: 27px;
}


.receipt-details span,
.receipt-items span {
  font-size: 9px;

  color: #7f91a8;
}


.receipt-details strong,
.receipt-items strong {
  font-size: 9px;

  color: #172033;

  text-align: right;
}


.receipt-status {
  color: #c77c00 !important;
}


.receipt-total {
  display: flex;
  align-items: center;
  justify-content: space-between;

  padding-top: 12px;
}


.receipt-total span {
  font-size: 11px;

  font-weight: 700;
}


.receipt-total strong {
  font-size: 19px;

  color: #2186a0;
}


.receipt-actions {
  display: flex;

  gap: 10px;

  margin-top: 20px;
}


.receipt-close,
.receipt-orders {
  flex: 1;

  height: 35px;

  border-radius: 7px;

  font-family: inherit;

  font-size: 9px;

  font-weight: 600;

  cursor: pointer;
}


.receipt-close {
  border: 1px solid #dce4ec;

  background: #ffffff;

  color: #52627a;
}


.receipt-orders {
  border: none;

  background: #2085a0;

  color: #ffffff;
}


/* =========================
   RESPONSIVE
========================= */

@media (max-width: 800px) {

  .sidebar {
    width: 190px;
  }

  .main-area {
    width: calc(100% - 190px);

    margin-left: 190px;
  }

  .content-container {
    padding: 24px 18px 35px;
  }

}


@media (max-width: 650px) {

  .sidebar {
    width: 70px;
  }

  .sidebar-logo {
    justify-content: center;

    padding: 0;
  }

  .logo-text {
    display: none;
  }

  .logo-droplet {
    margin-right: 0;
  }

  .nav-item {
    justify-content: center;

    padding: 0;
  }

  .nav-item img {
    margin-right: 0;
  }

  .nav-item span {
    display: none;
  }

  .main-area {
    width: calc(100% - 70px);

    margin-left: 70px;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .addon-row {
    align-items: flex-start;

    gap: 10px;
  }

  .addon-right {
    flex-direction: column;

    align-items: flex-end;

    gap: 4px;
  }

}

</style>