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
     <TopHeader />


      <!-- =========================
           CONTENT
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
                v-model="form.serviceId"
                :class="{ error: errors.service }"
              >

                <option value="">
                  Select service
                </option>

                <option
                  v-for="service in services"
                  :key="service.id"
                  :value="service.id"
                >
                  {{ service.service_name }}
                  - ₱{{ formatPrice(service.price_per_kg) }}/kg
                </option>

              </select>

              <span
                v-if="services.length === 0"
                class="helper-message"
              >
                No active services are currently available.
              </span>

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

                <span>
                  kg
                </span>

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
          <div
            class="addon-row"
            :class="{
              'addon-disabled':
                !addons.fabricConditioner.active
            }"
          >

            <label class="addon-left">

              <input
                v-model="addons.fabricConditioner.selected"
                type="checkbox"
                :disabled="!addons.fabricConditioner.active"
              />

              <span class="custom-checkbox"></span>

              <span class="addon-info">

                <strong>
                  Fabric Conditioner

                  <small
                    v-if="!addons.fabricConditioner.active"
                    class="inactive-label"
                  >
                    Inactive
                  </small>
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
                  :disabled="
                    !addons.fabricConditioner.active ||
                    !addons.fabricConditioner.selected
                  "
                >
                  −
                </button>

                <span>
                  {{ addons.fabricConditioner.quantity }}
                </span>

                <button
                  type="button"
                  @click="increaseAddon('fabricConditioner')"
                  :disabled="
                    !addons.fabricConditioner.active ||
                    !addons.fabricConditioner.selected
                  "
                >
                  +
                </button>

              </div>

              <strong class="addon-price">
                ₱{{ formatPrice(addons.fabricConditioner.price) }} each
              </strong>

            </div>

          </div>


          <!-- EXTRA DRYING -->
          <div
            class="addon-row"
            :class="{
              'addon-disabled':
                !addons.extraDrying.active
            }"
          >

            <label class="addon-left">

              <input
                v-model="addons.extraDrying.selected"
                type="checkbox"
                :disabled="!addons.extraDrying.active"
              />

              <span class="custom-checkbox"></span>

              <span class="addon-info">

                <strong>
                  Extra Drying

                  <small
                    v-if="!addons.extraDrying.active"
                    class="inactive-label"
                  >
                    Inactive
                  </small>
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
                  :disabled="
                    !addons.extraDrying.active ||
                    !addons.extraDrying.selected
                  "
                >
                  −
                </button>

                <span>
                  {{ addons.extraDrying.quantity }}
                </span>

                <button
                  type="button"
                  @click="increaseAddon('extraDrying')"
                  :disabled="
                    !addons.extraDrying.active ||
                    !addons.extraDrying.selected
                  "
                >
                  +
                </button>

              </div>

              <strong class="addon-price">
                ₱{{ formatPrice(addons.extraDrying.price) }} each
              </strong>

            </div>

          </div>

        </section>


        <!-- =========================
             ESTIMATED PICKUP
        ========================== -->
        <section class="form-card">

          <div class="section-title">

            <h2>
              Estimated Pickup
            </h2>

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
                :min="todayDate"
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
                {{ selectedService?.service_name || 'No service' }}
                {{ form.weight > 0 ? `${form.weight}kg` : '' }}
              </span>

              <strong>
                ₱{{ formatPrice(baseServiceCost) }}
              </strong>

            </div>


            <div
              v-if="
                addons.fabricConditioner.active &&
                addons.fabricConditioner.selected &&
                addons.fabricConditioner.quantity > 0
              "
              class="price-line"
            >

              <span>
                Fabric Conditioner
                x{{ addons.fabricConditioner.quantity }}
              </span>

              <strong>
                ₱{{ formatPrice(fabricConditionerCost) }}
              </strong>

            </div>


            <div
              v-if="
                addons.extraDrying.active &&
                addons.extraDrying.selected &&
                addons.extraDrying.quantity > 0
              "
              class="price-line"
            >

              <span>
                Extra Drying
                x{{ addons.extraDrying.quantity }}
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
            :disabled="
              creatingOrder ||
              services.length === 0
            "
          >
            {{
              creatingOrder
                ? 'Creating Order...'
                : 'Create Order'
            }}
          </button>

        </div>


        <div
          v-if="apiError"
          class="api-error"
        >
          {{ apiError }}
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
          @click="exitReceipt"
          aria-label="Close receipt"
        >
          ×
        </button>


        <div class="receipt-header">

          <div class="receipt-title">

            <span class="receipt-success-icon">
              ✓
            </span>

            <div>

              <h2>
                Order Receipt
              </h2>

              <p>
                Ticket: {{ createdOrder?.order_number }}
              </p>

            </div>

          </div>

        </div>


        <div class="receipt-divider teal-divider"></div>


        <div class="receipt-details">

          <div>
            <span>
              Customer Name
            </span>

            <strong>
              {{ createdOrder?.customer_name }}
            </strong>
          </div>

          <div>
            <span>
              Phone Number
            </span>

            <strong>
              {{ createdOrder?.phone_number }}
            </strong>
          </div>

          <div>
            <span>
              Main Service
            </span>

            <strong>
              {{ createdOrder?.service?.service_name }}
            </strong>
          </div>

          <div>
            <span>
              Load Weight
            </span>

            <strong>
              {{ createdOrder?.weight }} kg
            </strong>
          </div>

        </div>


        <div class="receipt-divider dashed-divider"></div>


        <section class="receipt-section">

          <h3>
            ADD-ONS SELECTED
          </h3>


          <div
            v-for="addon in createdOrder?.addons || []"
            :key="addon.id"
            class="receipt-addon-row"
          >

            <span>

              {{ addon.addon_name }}

              <small>
                x{{ addon.quantity }}
              </small>

            </span>

            <strong>
              ₱{{
                formatPrice(
                  Number(addon.addon_price) *
                  Number(addon.quantity)
                )
              }}
            </strong>

          </div>


          <p
            v-if="
              !createdOrder?.addons ||
              createdOrder.addons.length === 0
            "
            class="receipt-empty-addons"
          >
            No add-ons selected.
          </p>

        </section>


        <div class="receipt-divider dashed-divider"></div>


        <section class="receipt-section receipt-cost-breakdown">

          <h3>
            COST BREAKDOWN
          </h3>


          <div class="receipt-cost-row">

            <span>
              Base Service
              ({{ createdOrder?.service?.service_name }}
              {{ createdOrder?.weight }}kg)
            </span>

            <strong>
              ₱{{ formatPrice(receiptServiceCost) }}
            </strong>

          </div>


          <div
            v-for="addon in createdOrder?.addons || []"
            :key="`cost-${addon.id}`"
            class="receipt-cost-row"
          >

            <span>
              {{ addon.addon_name }}
            </span>

            <strong>
              ₱{{
                formatPrice(
                  Number(addon.addon_price) *
                  Number(addon.quantity)
                )
              }}
            </strong>

          </div>


          <div class="receipt-total">

            <span>
              Total Amount
            </span>

            <strong>
              ₱{{ formatPrice(createdOrder?.total_price) }}
            </strong>

          </div>

        </section>


        <div class="receipt-divider dashed-divider"></div>


        <div class="receipt-footer-details">

          <div>

            <span>
              Order Status
            </span>

            <strong class="receipt-status">

              <span class="receipt-status-dot"></span>

              {{ createdOrder?.status }}

            </strong>

          </div>


          <div>

            <span>
              Estimated Pickup
            </span>

            <strong>
              {{ formatPickup(createdOrder?.estimated_pickup) }}
            </strong>

          </div>

        </div>


        <div class="receipt-actions">

          <button
            type="button"
            class="receipt-print"
            @click="printReceipt"
          >
            ▣
            Print Receipt
          </button>

        </div>


        <p class="receipt-note">
          Order created successfully. Use X to return to the Orders list.
        </p>

      </div>

    </div>

  </div>
</template>


<script setup>
import {
  computed,
  onMounted,
  reactive,
  ref
} from 'vue'

import TopHeader from './TopHeader.vue'

const emit = defineEmits([
  'navigate',
  'logout',
  'order-created'
])


/* =========================
   DATE
========================= */

const getLocalDate = () => {
  const now = new Date()

  const year = now.getFullYear()

  const month = String(
    now.getMonth() + 1
  ).padStart(2, '0')

  const day = String(
    now.getDate()
  ).padStart(2, '0')

  return `${year}-${month}-${day}`
}

const todayDate = getLocalDate()

const currentDate = computed(() => {
  return new Date().toLocaleDateString(
    'en-PH',
    {
      month: 'short',
      day: 'numeric',
      year: 'numeric',
      weekday: 'long'
    }
  )
})


/* =========================
   DATA
========================= */

const services = ref([])

const createdOrder = ref(null)

const creatingOrder = ref(false)

const apiError = ref('')


/* =========================
   FORM
========================= */

const form = reactive({

  customerName: '',

  phone: '',

  serviceId: '',

  weight: 5,

  pickupDate: todayDate,

  pickupTime: '10:00'

})


/* =========================
   ADD-ONS
========================= */

const addons = reactive({

  fabricConditioner: {

    id: null,

    active: false,

    selected: false,

    quantity: 0,

    price: 0

  },

  extraDrying: {

    id: null,

    active: false,

    selected: false,

    quantity: 0,

    price: 0

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

const orderNumber = computed(() => {

  return (
    createdOrder.value?.order_number ||
    '—'
  )

})


/* =========================
   SELECTED SERVICE
========================= */

const selectedService = computed(() => {

  return services.value.find(
    (service) =>
      Number(service.id) ===
      Number(form.serviceId)
  ) || null

})


/* =========================
   SERVICE PRICE
========================= */

const servicePrice = computed(() => {

  return Number(
    selectedService.value?.price_per_kg ||
    0
  )

})


/* =========================
   BASE SERVICE COST
========================= */

const baseServiceCost = computed(() => {

  const weight =
    Number(form.weight) || 0

  return (
    servicePrice.value *
    weight
  )

})


/* =========================
   ADD-ON COSTS
========================= */

const fabricConditionerCost =
  computed(() => {

    if (
      !addons.fabricConditioner.active ||
      !addons.fabricConditioner.selected
    ) {
      return 0
    }

    return (
      addons.fabricConditioner.quantity *
      addons.fabricConditioner.price
    )

  })


const extraDryingCost =
  computed(() => {

    if (
      !addons.extraDrying.active ||
      !addons.extraDrying.selected
    ) {
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
   RECEIPT SERVICE COST
========================= */

const receiptServiceCost =
  computed(() => {

    if (!createdOrder.value) {
      return 0
    }

    const servicePrice =
      Number(
        createdOrder.value
          ?.service
          ?.price_per_kg || 0
      )

    const weight =
      Number(
        createdOrder.value
          ?.weight || 0
      )

    return servicePrice * weight

  })


/* =========================
   FORMAT PRICE
========================= */

const formatPrice = (value) => {

  return Number(
    value || 0
  ).toFixed(2)

}


/* =========================
   FORMAT PICKUP
========================= */

const formatPickup = (value) => {

  if (!value) {
    return 'Not set'
  }

  const date =
    new Date(value)

  if (
    Number.isNaN(
      date.getTime()
    )
  ) {
    return value
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

}


/* =========================
   AUTH HEADERS
========================= */

const authHeaders = () => {

  const token =
    localStorage.getItem(
      'washup_token'
    )

  return {

    Accept:
      'application/json',

    Authorization:
      `Bearer ${token}`

  }

}


/* =========================
   LOAD ORDER OPTIONS
========================= */

const loadOrderOptions =
  async () => {

    try {

      apiError.value = ''

      const [
        servicesResponse,
        addonsResponse
      ] = await Promise.all([

        fetch(
          '/api/services',
          {
            method: 'GET',
            headers:
              authHeaders()
          }
        ),

        fetch(
          '/api/addons',
          {
            method: 'GET',
            headers:
              authHeaders()
          }
        )

      ])


      /* =========================
         AUTH ERROR
      ========================== */

      if (
        servicesResponse.status === 401 ||
        addonsResponse.status === 401
      ) {

        apiError.value =
          'Your session has expired. Please log in again.'

        return

      }


      /* =========================
         SERVER ERROR
      ========================== */

      if (
        !servicesResponse.ok ||
        !addonsResponse.ok
      ) {

        throw new Error(
          'Could not load order options.'
        )

      }


      const allServices =
        await servicesResponse.json()

      const allAddons =
        await addonsResponse.json()


      /* =========================
         ONLY ACTIVE SERVICES
      ========================== */

      services.value =
        Array.isArray(allServices)
          ? allServices.filter(
              (service) =>
                service.is_active === true ||
                service.is_active === 1
            )
          : []


      /* =========================
         DEFAULT SERVICE
      ========================== */

      const washAndDry =
        services.value.find(
          (service) =>
            service.service_name ===
            'Wash & Dry'
        )

      form.serviceId =
        washAndDry?.id ||
        services.value[0]?.id ||
        ''


      /* =========================
         FIND ADD-ONS
      ========================== */

      const fabricConditioner =
        Array.isArray(allAddons)
          ? allAddons.find(
              (addon) =>
                addon.addon_name ===
                'Fabric Conditioner'
            )
          : null


      const extraDrying =
        Array.isArray(allAddons)
          ? allAddons.find(
              (addon) =>
                addon.addon_name ===
                'Extra Drying'
            )
          : null


      /* =========================
         FABRIC CONDITIONER
      ========================== */

      if (
        fabricConditioner &&
        (
          fabricConditioner.is_active === true ||
          fabricConditioner.is_active === 1
        )
      ) {

        addons.fabricConditioner.id =
          fabricConditioner.id

        addons.fabricConditioner.price =
          Number(
            fabricConditioner.addon_price
          )

        addons.fabricConditioner.active =
          true

        addons.fabricConditioner.selected =
          true

        addons.fabricConditioner.quantity =
          1

      } else {

        addons.fabricConditioner.id =
          null

        addons.fabricConditioner.price =
          0

        addons.fabricConditioner.active =
          false

        addons.fabricConditioner.selected =
          false

        addons.fabricConditioner.quantity =
          0

      }


      /* =========================
         EXTRA DRYING
      ========================== */

      if (
        extraDrying &&
        (
          extraDrying.is_active === true ||
          extraDrying.is_active === 1
        )
      ) {

        addons.extraDrying.id =
          extraDrying.id

        addons.extraDrying.price =
          Number(
            extraDrying.addon_price
          )

        addons.extraDrying.active =
          true

        addons.extraDrying.selected =
          true

        addons.extraDrying.quantity =
          1

      } else {

        addons.extraDrying.id =
          null

        addons.extraDrying.price =
          0

        addons.extraDrying.active =
          false

        addons.extraDrying.selected =
          false

        addons.extraDrying.quantity =
          0

      }

    } catch (error) {

      console.error(
        'Order options error:',
        error
      )

      apiError.value =
        'Unable to load services and add-ons. Please try again.'

    }

}


/* =========================
   ADD-ON QUANTITY
========================= */

const increaseAddon =
  (addonName) => {

    const addon =
      addons[addonName]

    if (!addon.active) {
      return
    }

    addon.selected = true

    addon.quantity += 1

  }


const decreaseAddon =
  (addonName) => {

    const addon =
      addons[addonName]

    if (!addon.active) {
      return
    }

    if (
      addon.quantity > 1
    ) {

      addon.quantity -= 1

    } else {

      addon.quantity = 0

      addon.selected = false

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


  if (
    !form.customerName.trim()
  ) {

    errors.customerName =
      'Customer name is required.'

    valid = false

  }


  if (
    !form.phone.trim()
  ) {

    errors.phone =
      'Phone number is required.'

    valid = false

  }


  if (!form.serviceId) {

    errors.service =
      'Please select a service.'

    valid = false

  }


  if (
    !form.weight ||
    Number(form.weight) <= 0
  ) {

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


  /*
   * Make sure the selected service
   * is still active.
   */
  const serviceStillActive =
    services.value.some(
      (service) =>
        Number(service.id) ===
        Number(form.serviceId)
    )

  if (
    form.serviceId &&
    !serviceStillActive
  ) {

    errors.service =
      'The selected service is no longer active.'

    valid = false

  }


  return valid

}


/* =========================
   SERVER VALIDATION ERRORS
========================= */

const showServerErrors =
  (serverErrors) => {

    const fieldMap = {

      customer_name:
        'customerName',

      phone_number:
        'phone',

      service_id:
        'service',

      weight:
        'weight',

      estimated_pickup:
        'pickupDate'

    }


    Object.entries(
      serverErrors || {}
    ).forEach(
      ([field, messages]) => {

        const formField =
          fieldMap[field]

        if (formField) {

          errors[formField] =
            Array.isArray(messages)
              ? messages[0]
              : messages

        }

      }
    )

  }


/* =========================
   CREATE ORDER
========================= */

const createOrder =
  async () => {

    if (
      creatingOrder.value ||
      !validateForm()
    ) {
      return
    }


    apiError.value = ''

    creatingOrder.value =
      true


    /*
     * Only active and selected
     * add-ons are sent to Laravel.
     */
    const selectedAddons =
      Object.values(addons)

        .filter(
          (addon) =>
            addon.active &&
            addon.selected &&
            addon.quantity > 0 &&
            addon.id
        )

        .map(
          (addon) => ({
            addon_id:
              addon.id,

            quantity:
              addon.quantity
          })
        )


    try {

      const response =
        await fetch(
          '/api/orders',
          {

            method: 'POST',

            headers: {

              ...authHeaders(),

              'Content-Type':
                'application/json'

            },

            body:
              JSON.stringify({

                customer_name:
                  form.customerName,

                phone_number:
                  form.phone,

                service_id:
                  form.serviceId,

                weight:
                  form.weight,

                estimated_pickup:
                  `${form.pickupDate}T${form.pickupTime}`,

                addons:
                  selectedAddons

              })

          }
        )


      let data = null

      try {

        data =
          await response.json()

      } catch {

        data = {}

      }


      /* =========================
         SESSION EXPIRED
      ========================== */

      if (
        response.status === 401
      ) {

        apiError.value =
          'Your session has expired. Please log in again.'

        return

      }


      /* =========================
         VALIDATION / BUSINESS RULE
      ========================== */

      if (
        response.status === 422
      ) {

        showServerErrors(
          data?.errors
        )

        apiError.value =
          data?.message ||
          'Please correct the highlighted fields and try again.'

        return

      }


      /* =========================
         OTHER SERVER ERROR
      ========================== */

      if (!response.ok) {

        console.error(
          'Create order failed:',
          response.status,
          data
        )

        apiError.value =
          data?.message ||
          `Unable to create the order. Server returned ${response.status}.`

        return

      }


      /* =========================
         SUCCESS
      ========================== */

      createdOrder.value =
        data


      /*
       * Tell App.vue that a new
       * order was created.
       */
      emit(
        'order-created',
        data
      )


      /*
       * Show receipt.
       */
      showReceipt.value =
        true


    } catch (error) {

      console.error(
        'Create order network error:',
        error
      )

      apiError.value =
        'Unable to connect to the server. Please make sure the Laravel server is running.'

    } finally {

      creatingOrder.value =
        false

    }

}


/* =========================
   RECEIPT EXIT
========================= */

const exitReceipt = () => {

  showReceipt.value =
    false

  emit(
    'navigate',
    'Orders'
  )

}


/* =========================
   PRINT RECEIPT
========================= */

const printReceipt = () => {

  window.print()

}


/* =========================
   NAVIGATION
========================= */

const goTo = (page) => {

  emit(
    'navigate',
    page
  )

}


/* =========================
   LOGOUT
========================= */

const handleLogout =
  async () => {

    const token =
      localStorage.getItem(
        'washup_token'
      )


    try {

      if (token) {

        await fetch(
          '/api/logout',
          {

            method: 'POST',

            headers: {

              Accept:
                'application/json',

              Authorization:
                `Bearer ${token}`

            }

          }
        )

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

      emit(
        'logout'
      )

    }

  }


/* =========================
   INITIAL LOAD
========================= */

onMounted(() => {

  loadOrderOptions()

})

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


.helper-message {
  display: block;

  margin-top: 5px;

  font-size: 8px;

  color: #8b99aa;
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

  transition:
    opacity 0.15s ease,
    background 0.15s ease;
}


.addon-row:last-child {
  margin-bottom: 0;
}


.addon-disabled {
  opacity: 0.55;

  background: #f2f4f7;
}


.addon-left {
  display: flex;
  align-items: center;

  cursor: pointer;
}


.addon-left:has(input:disabled) {
  cursor: not-allowed;
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


.addon-info .inactive-label {
  display: inline-block;

  margin-top: 0;
  margin-left: 6px;

  padding: 2px 5px;

  border-radius: 8px;

  background: #e8edf2;

  color: #7c8795;

  font-size: 7px;

  font-weight: 600;
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


.create-button:hover:not(:disabled) {
  background: #19768f;
}


.create-button:disabled {
  opacity: 0.6;

  cursor: not-allowed;
}


.api-error {
  margin-top: -5px;
  margin-bottom: 15px;

  padding: 10px 12px;

  border: 1px solid #f1c7c7;

  border-radius: 6px;

  background: #fff5f5;

  color: #b44a4a;

  font-size: 9px;
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

  max-width: 480px;

  max-height: 90vh;

  overflow-y: auto;

  padding: 31px 32px 12px;

  background: #ffffff;

  border-radius: 15px;

  box-shadow:
    0 18px 45px rgba(16, 24, 40, 0.2);
}


.close-modal {
  position: absolute;

  top: 31px;
  right: 31px;

  width: 29px;
  height: 29px;

  border: none;

  border-radius: 50%;

  background: #f7f9fc;

  color: #7f91a8;

  font-size: 15px;

  line-height: 1;

  cursor: pointer;
}


.close-modal:hover {
  background: #edf1f5;

  color: #172033;
}


.receipt-header {
  text-align: left;
}


.receipt-title {
  display: flex;

  align-items: center;

  gap: 11px;
}


.receipt-success-icon {
  width: 25px;
  height: 25px;

  display: flex;
  align-items: center;
  justify-content: center;

  border-radius: 50%;

  background: #d8faf1;

  color: #089b8b;

  font-size: 14px;

  font-weight: 700;
}


.receipt-title h2 {
  margin: 0;

  color: #172033;

  font-size: 16px;

  font-weight: 700;
}


.receipt-title p {
  margin: 3px 0 0;

  color: #0e9f8e;

  font-size: 11px;

  font-weight: 700;
}


.receipt-divider {
  height: 1px;

  margin: 18px 0;

  background: #e4e9ef;
}


.receipt-divider.teal-divider {
  height: 3px;

  margin: 17px 0 18px;

  border-radius: 4px;

  background: #089b8b;
}


.receipt-divider.dashed-divider {
  height: 1px;

  margin: 16px 0;

  border-top: 1px dashed #dbe3ec;

  background: transparent;
}


.receipt-details > div,
.receipt-addon-row,
.receipt-cost-row,
.receipt-footer-details > div {
  display: flex;

  align-items: center;
  justify-content: space-between;

  min-height: 27px;
}


.receipt-details span,
.receipt-addon-row span,
.receipt-cost-row span,
.receipt-footer-details span {
  color: #667a99;

  font-size: 11px;
}


.receipt-details strong,
.receipt-addon-row strong,
.receipt-cost-row strong,
.receipt-footer-details strong {
  color: #172033;

  font-size: 11px;

  font-weight: 600;

  text-align: right;
}


.receipt-section h3 {
  margin: 0 0 7px;

  color: #667a99;

  font-size: 10px;

  font-weight: 700;
}


.receipt-addon-row small {
  display: inline-block;

  margin-left: 7px;

  padding: 2px 5px;

  border-radius: 3px;

  background: #f1f5f9;

  color: #667a99;

  font-size: 8px;

  font-weight: 600;
}


.receipt-empty-addons {
  margin: 0;

  color: #667a99;

  font-size: 10px;
}


.receipt-total {
  display: flex;

  align-items: center;

  justify-content: space-between;

  margin-top: 7px;

  padding-top: 11px;

  border-top: 1px solid #e5eaf0;
}


.receipt-total span {
  color: #172033;

  font-size: 13px;
}


.receipt-total strong {
  color: #089b8b;

  font-size: 16px;
}


.receipt-status {
  color: #c77c00 !important;
}


.receipt-status-dot {
  display: inline-block;

  width: 6px;
  height: 6px;

  margin-right: 5px;

  border-radius: 50%;

  background: #f0a000;
}


.receipt-actions {
  display: block;

  margin-top: 26px;
}


.receipt-print {
  width: 100%;

  height: 44px;

  border: 1px solid #079e8e;

  border-radius: 8px;

  background: #ffffff;

  color: #079e8e;

  font-family: inherit;

  font-size: 12px;

  font-weight: 700;

  cursor: pointer;
}


.receipt-print:hover {
  background: #f1fbf9;
}


.receipt-note {
  margin: 13px 0 0;

  color: #9aa9bd;

  font-size: 9px;

  text-align: center;
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