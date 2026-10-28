<template>
  <div class="dashboard">

    <!-- =========================================
         SIDEBAR
    ========================================== -->
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

          <span>
            Dashboard
          </span>
        </button>


        <button
          type="button"
          class="nav-item"
          @click="goTo('Orders')"
        >
          <img
            src="../assets/iconordergray.png"
            alt=""
          />

          <span>
            Orders
          </span>
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

          <span>
            Services &amp; Add-ons
          </span>
        </button>


        <button
          type="button"
          class="nav-item active"
        >
          <img
            src="../assets/iconreportsgray.png"
            alt=""
          />

          <span>
            Reports
          </span>
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

          <span>
            Logout
          </span>
        </button>

      </div>

    </aside>


    <!-- =========================================
         MAIN CONTENT
    ========================================== -->
    <main class="main-content">

      <!-- SHARED HEADER -->
      <TopHeader />


      <!-- =======================================
           PAGE CONTENT
      ======================================== -->
      <div class="content">


        <!-- PAGE HEADER -->
        <div class="page-header">

          <div>

            <h1>
              Reports
            </h1>

            <p>
              View transaction and customer feedback summaries.
            </p>

          </div>

        </div>


        <!-- =======================================
             DATE FILTER
        ======================================== -->
        <section class="filter-card">

          <div class="filter-label">
            Date Range
          </div>


          <div class="date-controls">

            <div class="date-input-group">

              <label>
                From
              </label>

              <input
                v-model="startDate"
                type="date"
                :max="endDate"
              />

            </div>


            <span class="date-separator">
              —
            </span>


            <div class="date-input-group">

              <label>
                To
              </label>

              <input
                v-model="endDate"
                type="date"
                :min="startDate"
              />

            </div>


            <button
              type="button"
              class="apply-button"
              :disabled="loading"
              @click="loadAllReports"
            >
              <span v-if="loading">
                Loading...
              </span>

              <span v-else>
                Apply Filter
              </span>
            </button>

          </div>

        </section>


        <!-- =======================================
             TABS
        ======================================== -->
        <div class="report-tabs">

          <button
            type="button"
            :class="[
              'report-tab',
              {
                active:
                  activeTab === 'feedback'
              }
            ]"
            @click="activeTab = 'feedback'"
          >
            Customer Feedback
          </button>


          <button
            type="button"
            :class="[
              'report-tab',
              {
                active:
                  activeTab === 'sales'
              }
            ]"
            @click="activeTab = 'sales'"
          >
            Orders &amp; Sales
          </button>

        </div>


        <!-- =======================================
             ERROR MESSAGE
        ======================================== -->
        <div
          v-if="errorMessage"
          class="error-card"
        >

          <div class="error-icon">
            !
          </div>


          <div>

            <strong>
              Unable to load report
            </strong>

            <p>
              {{ errorMessage }}
            </p>

          </div>

        </div>


        <!-- =======================================
             CUSTOMER FEEDBACK TAB
        ======================================== -->
        <template v-if="activeTab === 'feedback'">

          <div class="section-heading">
            <span>
              CUSTOMER FEEDBACK
            </span>
          </div>


          <!-- FEEDBACK SUMMARY -->
          <section class="feedback-summary-grid">


            <!-- TOTAL FEEDBACK -->
            <article class="feedback-summary-card">

              <div class="summary-card-top">

                <span>
                  Total Feedback
                </span>


                <div
                  class="feedback-card-icon feedback-blue"
                >
                  <span class="feedback-bubble">
                    ○
                  </span>
                </div>

              </div>


              <div class="summary-value">
                {{ feedbackSummary.total_feedback }}
              </div>


              <div class="summary-description">
                customer responses
              </div>

            </article>


            <!-- AVERAGE RATING -->
            <article class="feedback-summary-card">

              <div class="summary-card-top">

                <span>
                  Average Rating
                </span>


                <div
                  class="feedback-card-icon feedback-yellow"
                >
                  <span class="star-symbol">
                    ☆
                  </span>
                </div>

              </div>


              <div class="rating-value-row">

                <div class="summary-value">
                  {{ feedbackSummary.average_rating }}
                </div>

                <span class="rating-max">
                  /5
                </span>

              </div>


              <div class="stars-row">

                <span
                  v-for="star in 5"
                  :key="star"
                  :class="[
                    'star',
                    {
                      filled:
                        star <= roundedAverageRating
                    }
                  ]"
                >
                  ★
                </span>

              </div>

            </article>


            <!-- COMPLAINTS -->
            <article class="feedback-summary-card">

              <div class="summary-card-top">

                <span>
                  Complaints
                </span>


                <div
                  class="feedback-card-icon feedback-red"
                >
                  <span>
                    !
                  </span>
                </div>

              </div>


              <div class="summary-value">
                {{ feedbackSummary.complaints }}
              </div>


              <div class="summary-description">
                customer concerns
              </div>

            </article>

          </section>


          <!-- FEEDBACK TABLE -->
          <section class="feedback-table-card">

            <div class="table-wrapper">

              <table>

                <thead>

                  <tr>

                    <th>
                      Order No.
                    </th>

                    <th>
                      Customer
                    </th>

                    <th>
                      Rating
                    </th>

                    <th>
                      Type
                    </th>

                    <th>
                      Comment
                    </th>

                    <th>
                      Date
                    </th>

                    <th>
                      Action
                    </th>

                  </tr>

                </thead>


                <tbody>

                  <!-- REAL FEEDBACK -->
                  <tr
                    v-for="feedback in feedbackRows"
                    :key="feedback.id"
                  >

                    <td class="order-number-cell">
                      {{ feedback.order_number || '—' }}
                    </td>


                    <td>
                      {{ feedback.customer_name || '—' }}
                    </td>


                    <td>

                      <div class="rating-stars">

                        <span
                          v-for="star in 5"
                          :key="star"
                          :class="[
                            'table-star',
                            {
                              filled:
                                star <= feedback.rating
                            }
                          ]"
                        >
                          ★
                        </span>

                      </div>

                    </td>


                    <td>

                      <span
                        :class="[
                          'type-pill',
                          getFeedbackTypeClass(
                            feedback.type
                          )
                        ]"
                      >
                        {{ feedback.type }}
                      </span>

                    </td>


                    <td class="comment-cell">
                      {{ truncateComment(feedback.comment) }}
                    </td>


                    <td>
                      {{
                        formatFeedbackDate(
                          feedback.created_at
                        )
                      }}
                    </td>


                    <td>

                      <button
                        type="button"
                        class="view-button"
                        @click="viewFeedback(feedback)"
                      >

                        <span class="view-icon">
                          ◉
                        </span>

                        View

                      </button>

                    </td>

                  </tr>


                  <!-- EMPTY STATE -->
                  <tr
                    v-if="
                      reportHydrated &&
                      !loading &&
                      feedbackRows.length === 0
                    "
                  >

                    <td
                      colspan="7"
                      class="empty-table-cell"
                    >

                      <div class="empty-feedback">

                        <div class="empty-feedback-icon">
                          💬
                        </div>

                        <strong>
                          No customer feedback yet
                        </strong>

                        <p>
                          Feedback submitted by customers
                          will appear here.
                        </p>

                      </div>

                    </td>

                  </tr>


                  <!-- LOADING STATE -->
                  <tr
                    v-if="
                      loading &&
                      !reportHydrated &&
                      feedbackRows.length === 0
                    "
                  >

                    <td
                      colspan="7"
                      class="empty-table-cell"
                    >

                      <div class="empty-feedback">

                        <div class="table-spinner"></div>

                        <strong>
                          Loading feedback...
                        </strong>

                      </div>

                    </td>

                  </tr>

                </tbody>

              </table>

            </div>

          </section>

        </template>


        <!-- =======================================
             ORDERS & SALES TAB
        ======================================== -->
        <template v-else>

          <div class="section-heading">
            <span>
              ORDERS &amp; SALES
            </span>
          </div>


          <!-- SALES SUMMARY -->
          <section class="sales-summary-grid">


            <!-- TOTAL ORDERS -->
            <article class="sales-summary-card">

              <div class="summary-card-top">

                <span>
                  Total Orders
                </span>


                <div class="sales-icon blue-icon">

                  <img
                    src="../assets/icontotalorder.png"
                    alt=""
                  />

                </div>

              </div>


              <div class="summary-value">
                {{ summary.total_orders }}
              </div>


              <div class="summary-description">
                selected period
              </div>

            </article>


            <!-- TOTAL REVENUE -->
            <article class="sales-summary-card">

              <div class="summary-card-top">

                <span>
                  Total Revenue
                </span>


                <div class="sales-icon green-icon">

                  <img
                    src="../assets/icontotalrevenue.png"
                    alt=""
                  />

                </div>

              </div>


              <div class="summary-value">
                {{ formatCurrency(summary.total_revenue) }}
              </div>


              <div class="summary-description">
                selected period
              </div>

            </article>


            <!-- PENDING -->
            <article class="sales-summary-card">

              <div class="summary-card-top">

                <span>
                  Pending
                </span>


                <div class="status-count-icon pending-icon">
                  •
                </div>

              </div>


              <div class="summary-value">
                {{ summary.pending }}
              </div>


              <div class="summary-description">
                processing
              </div>

            </article>


            <!-- READY -->
            <article class="sales-summary-card">

              <div class="summary-card-top">

                <span>
                  Ready for Pickup
                </span>


                <div class="status-count-icon ready-icon">
                  ✓
                </div>

              </div>


              <div class="summary-value">
                {{ summary.ready_for_pickup }}
              </div>


              <div class="summary-description">
                waiting for pickup
              </div>

            </article>


            <!-- CLAIMED -->
            <article class="sales-summary-card">

              <div class="summary-card-top">

                <span>
                  Claimed
                </span>


                <div class="sales-icon purple-icon">

                  <img
                    src="../assets/iconclaimedorders.png"
                    alt=""
                  />

                </div>

              </div>


              <div class="summary-value">
                {{ summary.claimed }}
              </div>


              <div class="summary-description">
                completed
              </div>

            </article>

          </section>


          <!-- =======================================
               CHARTS
          ======================================== -->
          <section class="charts-grid">


            <!-- ORDERS OVER TIME -->
            <article class="chart-card">

              <div class="chart-header">

                <div>

                  <h2>
                    Orders Over Time
                  </h2>

                  <p>
                    Daily order count for the selected period.
                  </p>

                </div>


                <span class="chart-label orders-label">
                  Orders
                </span>

              </div>


              <div class="chart-container">

                <canvas
                  ref="ordersChartCanvas"
                ></canvas>

              </div>

            </article>


            <!-- REVENUE OVER TIME -->
            <article class="chart-card">

              <div class="chart-header">

                <div>

                  <h2>
                    Revenue Over Time
                  </h2>

                  <p>
                    Daily revenue for the selected period.
                  </p>

                </div>


                <span class="chart-label revenue-label">
                  Revenue
                </span>

              </div>


              <div class="chart-container">

                <canvas
                  ref="revenueChartCanvas"
                ></canvas>

              </div>

            </article>


          </section>

        </template>

      </div>

    </main>


    <!-- =========================================
         FULL FEEDBACK MODAL
    ========================================== -->
    <div
      v-if="selectedFeedback"
      class="modal-overlay"
      @click.self="closeFeedback"
    >

      <div class="feedback-modal">


        <!-- MODAL HEADER -->
        <div class="modal-header">

          <div>

            <span class="modal-kicker">
              CUSTOMER FEEDBACK
            </span>

            <h2>
              Feedback Details
            </h2>

          </div>


          <button
            type="button"
            class="modal-close"
            @click="closeFeedback"
          >
            ×
          </button>

        </div>


        <!-- MODAL BODY -->
        <div class="modal-body">


          <div class="feedback-detail-grid">

            <div class="detail-item">

              <span>
                Order No.
              </span>

              <strong>
                {{
                  selectedFeedback.order_number ||
                  '—'
                }}
              </strong>

            </div>


            <div class="detail-item">

              <span>
                Customer
              </span>

              <strong>
                {{
                  selectedFeedback.customer_name ||
                  '—'
                }}
              </strong>

            </div>


            <div class="detail-item">

              <span>
                Type
              </span>

              <strong>
                {{ selectedFeedback.type }}
              </strong>

            </div>


            <div class="detail-item">

              <span>
                Date
              </span>

              <strong>
                {{
                  formatFeedbackDate(
                    selectedFeedback.created_at
                  )
                }}
              </strong>

            </div>

          </div>


          <!-- RATING -->
          <div class="modal-rating">

            <span class="detail-label">
              Rating
            </span>


            <div class="modal-stars">

              <span
                v-for="star in 5"
                :key="star"
                :class="[
                  'modal-star',
                  {
                    filled:
                      star <= selectedFeedback.rating
                  }
                ]"
              >
                ★
              </span>


              <span class="modal-rating-number">
                {{
                  selectedFeedback.rating
                }}/5
              </span>

            </div>

          </div>


          <!-- COMMENT -->
          <div class="comment-section">

            <span class="detail-label">
              Customer Comment
            </span>


            <div class="full-comment">
              {{ selectedFeedback.comment }}
            </div>

          </div>

        </div>


        <!-- MODAL FOOTER -->
        <div class="modal-footer">

          <button
            type="button"
            class="close-button"
            @click="closeFeedback"
          >
            Close
          </button>

        </div>

      </div>

    </div>

  </div>
</template>


<script setup>

import {
  ref,
  computed,
  nextTick,
  watch,
  onMounted,
  onBeforeUnmount
} from 'vue'


import TopHeader from './TopHeader.vue'


import {
  Chart,
  BarController,
  BarElement,
  LineController,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  Tooltip,
  Legend,
  Filler
} from 'chart.js'


Chart.register(
  BarController,
  BarElement,
  LineController,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  Tooltip,
  Legend,
  Filler
)


/* =========================================
   EMITS
========================================= */

const emit = defineEmits([
  'navigate',
  'logout'
])


/* =========================================
   NAVIGATION
========================================= */

const goTo = (page) => {
  emit('navigate', page)
}


const handleLogout = () => {
  emit('logout')
}


/* =========================================
   DATE
========================================= */

const getLocalDate = () => {

  const now = new Date()

  const year =
    now.getFullYear()

  const month =
    String(
      now.getMonth() + 1
    ).padStart(2, '0')

  const day =
    String(
      now.getDate()
    ).padStart(2, '0')

  return `${year}-${month}-${day}`
}


const getStartOfMonth = () => {

  const now = new Date()

  const year =
    now.getFullYear()

  const month =
    String(
      now.getMonth() + 1
    ).padStart(2, '0')

  return `${year}-${month}-01`
}


const startDate =
  ref(getStartOfMonth())


const endDate =
  ref(getLocalDate())


/* =========================================
   TAB
========================================= */

const activeTab =
  ref('feedback')


/* =========================================
   REPORT STATE
========================================= */

const loading =
  ref(false)


const errorMessage =
  ref('')


const summary =
  ref({

    total_orders: 0,

    total_revenue: 0,

    pending: 0,

    ready_for_pickup: 0,

    claimed: 0

  })


const ordersOverTime =
  ref([])


const revenueOverTime =
  ref([])


/* =========================================
   FEEDBACK STATE
========================================= */

const feedbackRows =
  ref([])


const feedbackSummary =
  ref({

    total_feedback: 0,

    average_rating: 0,

    complaints: 0

  })


const selectedFeedback =
  ref(null)


/* =========================================
   CHART REFS
========================================= */

const ordersChartCanvas =
  ref(null)


const revenueChartCanvas =
  ref(null)


let ordersChart =
  null


let revenueChart =
  null


/* =========================================
   REPORT CACHE
========================================= */

const reportHydrated =
  ref(false)


const getReportCacheKey = (
  start,
  end
) => {
  return `washup_reports_${start}_${end}`
}


const applyCachedReport = (
  cached
) => {

  if (!cached) {
    return false
  }

  summary.value = {
    total_orders:
      Number(
        cached.summary?.total_orders ??
        0
      ),

    total_revenue:
      Number(
        cached.summary?.total_revenue ??
        0
      ),

    pending:
      Number(
        cached.summary?.pending ??
        0
      ),

    ready_for_pickup:
      Number(
        cached.summary?.ready_for_pickup ??
        0
      ),

    claimed:
      Number(
        cached.summary?.claimed ??
        0
      )
  }


  ordersOverTime.value =
    Array.isArray(
      cached.ordersOverTime
    )
      ? cached.ordersOverTime
      : []


  revenueOverTime.value =
    Array.isArray(
      cached.revenueOverTime
    )
      ? cached.revenueOverTime
      : []


  feedbackSummary.value = {
    total_feedback:
      Number(
        cached.feedbackSummary?.total_feedback ??
        0
      ),

    average_rating:
      Number(
        cached.feedbackSummary?.average_rating ??
        0
      ),

    complaints:
      Number(
        cached.feedbackSummary?.complaints ??
        0
      )
  }


  feedbackRows.value =
    Array.isArray(
      cached.feedbackRows
    )
      ? cached.feedbackRows
      : []


  reportHydrated.value =
    true


  return true
}


const loadCachedReports = () => {

  const key =
    getReportCacheKey(
      startDate.value,
      endDate.value
    )


  const raw =
    localStorage.getItem(key)


  if (!raw) {
    return false
  }


  try {

    const cached =
      JSON.parse(raw)

    return applyCachedReport(
      cached
    )

  } catch (error) {

    console.warn(
      'Invalid report cache:',
      error
    )

    localStorage.removeItem(key)

    return false
  }
}


const saveReportCache = () => {

  const key =
    getReportCacheKey(
      startDate.value,
      endDate.value
    )


  const payload = {
    saved_at: Date.now(),
    summary: summary.value,
    ordersOverTime:
      ordersOverTime.value,
    revenueOverTime:
      revenueOverTime.value,
    feedbackSummary:
      feedbackSummary.value,
    feedbackRows:
      feedbackRows.value
  }


  try {

    localStorage.setItem(
      key,
      JSON.stringify(payload)
    )

  } catch (error) {

    console.warn(
      'Unable to save report cache:',
      error
    )
  }
}


/* =========================================
   FEEDBACK COMPUTED
========================================= */

const roundedAverageRating =
  computed(() => {

    return Math.round(
      Number(
        feedbackSummary.value.average_rating ||
        0
      )
    )

  })


/* =========================================
   FORMATTING
========================================= */

const formatCurrency = (
  amount
) => {

  const value =
    Number(amount || 0)

  return `₱${value.toLocaleString(
    'en-PH',
    {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    }
  )}`

}


const formatFeedbackDate = (
  dateString
) => {

  if (!dateString) {
    return '—'
  }

  const date =
    new Date(dateString)

  if (
    Number.isNaN(
      date.getTime()
    )
  ) {
    return '—'
  }

  return date.toLocaleDateString(
    'en-PH',
    {
      day: '2-digit',
      month: 'short',
      year: 'numeric'
    }
  )

}


const truncateComment = (
  comment
) => {

  if (!comment) {
    return '—'
  }

  const text =
    String(comment)

  if (text.length <= 34) {
    return text
  }

  return `${text.slice(0, 34)}...`

}


/* =========================================
   FEEDBACK TYPE
========================================= */

const getFeedbackTypeClass = (
  type
) => {

  const normalized =
    String(
      type || ''
    ).toLowerCase()


  if (
    normalized === 'complaint'
  ) {
    return 'complaint-type'
  }


  if (
    normalized === 'suggestion'
  ) {
    return 'suggestion-type'
  }


  return 'feedback-type'

}


/* =========================================
   VIEW FEEDBACK
========================================= */

const viewFeedback = (
  feedback
) => {

  selectedFeedback.value =
    feedback

}


const closeFeedback = () => {

  selectedFeedback.value =
    null

}


/* =========================================
   LOAD SALES REPORT
========================================= */

const loadSalesReport = async () => {

  const token =
    localStorage.getItem(
      'washup_token'
    )


  if (!token) {
    throw new Error(
      'Your session has expired. Please log in again.'
    )
  }


  const params =
    new URLSearchParams({

      start_date:
        startDate.value,

      end_date:
        endDate.value

    })


  const response =
    await fetch(
      `/api/reports?${params.toString()}`,
      {
        method: 'GET',

        headers: {
          Accept:
            'application/json',

          Authorization:
            `Bearer ${token}`
        }
      }
    )


  if (
    response.status === 401
  ) {

    throw new Error(
      'Your session has expired. Please log in again.'
    )

  }


  if (!response.ok) {

    let message =
      'Failed to load sales report.'


    try {

      const errorData =
        await response.json()

      if (
        errorData.message
      ) {

        message =
          errorData.message

      }

    } catch {}


    throw new Error(message)

  }


  const data =
    await response.json()


  summary.value = {

    total_orders:
      Number(
        data.summary?.total_orders ??
        0
      ),

    total_revenue:
      Number(
        data.summary?.total_revenue ??
        0
      ),

    pending:
      Number(
        data.summary?.pending ??
        0
      ),

    ready_for_pickup:
      Number(
        data.summary?.ready_for_pickup ??
        0
      ),

    claimed:
      Number(
        data.summary?.claimed ??
        0
      )

  }


  ordersOverTime.value =
    Array.isArray(
      data.orders_over_time
    )
      ? data.orders_over_time
      : []


  revenueOverTime.value =
    Array.isArray(
      data.revenue_over_time
    )
      ? data.revenue_over_time
      : []

}


/* =========================================
   LOAD FEEDBACK REPORT
========================================= */

const loadFeedbackReport = async () => {

  const token =
    localStorage.getItem(
      'washup_token'
    )


  if (!token) {
    throw new Error(
      'Your session has expired. Please log in again.'
    )
  }


  const params =
    new URLSearchParams({

      start_date:
        startDate.value,

      end_date:
        endDate.value

    })


  const response =
    await fetch(
      `/api/feedbacks?${params.toString()}`,
      {
        method: 'GET',

        headers: {
          Accept:
            'application/json',

          Authorization:
            `Bearer ${token}`
        }
      }
    )


  if (
    response.status === 401
  ) {

    throw new Error(
      'Your session has expired. Please log in again.'
    )

  }


  if (!response.ok) {

    let message =
      'Failed to load customer feedback.'


    try {

      const errorData =
        await response.json()

      if (
        errorData.message
      ) {

        message =
          errorData.message

      }

    } catch {}


    throw new Error(message)

  }


  const data =
    await response.json()


  feedbackSummary.value = {

    total_feedback:
      Number(
        data.summary?.total_feedback ??
        0
      ),

    average_rating:
      Number(
        data.summary?.average_rating ??
        0
      ),

    complaints:
      Number(
        data.summary?.complaints ??
        0
      )

  }


  feedbackRows.value =
    Array.isArray(
      data.feedback
    )
      ? data.feedback
      : []

}


/* =========================================
   LOAD EVERYTHING
========================================= */

const loadAllReports = async ({
  showLoading = true
} = {}) => {

  if (!startDate.value || !endDate.value) {

    errorMessage.value =
      'Please select both a start date and an end date.'

    return
  }


  if (
    startDate.value >
    endDate.value
  ) {

    errorMessage.value =
      'Start date cannot be later than end date.'

    return
  }


  if (showLoading) {
    loading.value = true
  }

  errorMessage.value = ''


  try {

    await Promise.all([
      loadSalesReport(),
      loadFeedbackReport()
    ])


    saveReportCache()

    reportHydrated.value =
      true


    await nextTick()


    if (
      activeTab.value === 'sales'
    ) {

      createOrdersChart()

      createRevenueChart()

    }

  } catch (error) {

    console.error(
      'Failed to load reports:',
      error
    )


    errorMessage.value =
      error.message ||
      'Unable to load report data. Please try again.'

  } finally {

    if (showLoading) {
      loading.value = false
    }

  }

}


/* =========================================
   SALES TAB WATCH
========================================= */

watch(
  activeTab,
  async (tab) => {

    if (
      tab !== 'sales'
    ) {
      return
    }


    await nextTick()


    createOrdersChart()

    createRevenueChart()

  }
)


/* =========================================
   ORDERS CHART
========================================= */

const createOrdersChart = () => {

  if (
    !ordersChartCanvas.value
  ) {

    return

  }


  if (ordersChart) {

    ordersChart.destroy()

    ordersChart =
      null

  }


  const labels =
    ordersOverTime.value.map(
      item =>
        item.label
    )


  const values =
    ordersOverTime.value.map(
      item =>
        Number(
          item.orders || 0
        )
    )


  ordersChart =
    new Chart(
      ordersChartCanvas.value,
      {

        type: 'bar',


        data: {

          labels,


          datasets: [

            {

              label:
                'Orders',

              data:
                values,

              backgroundColor:
                '#247f99',

              borderRadius:
                5,

              borderSkipped:
                false,

              barPercentage:
                0.58,

              categoryPercentage:
                0.72

            }

          ]

        },


        options: {

          responsive:
            true,

          maintainAspectRatio:
            false,


          plugins: {

            legend: {
              display: false
            },


            tooltip: {

              backgroundColor:
                '#172033',

              padding:
                10,


              callbacks: {

                label:
                  context =>
                    ` Orders: ${context.raw}`

              }

            }

          },


          scales: {

            x: {

              grid: {
                display: false
              },

              border: {
                display: false
              },

              ticks: {

                color:
                  '#8f9cae',

                maxRotation:
                  0,

                autoSkip:
                  true,

                maxTicksLimit:
                  10,

                font: {
                  size: 10
                }

              }

            },


            y: {

              beginAtZero:
                true,

              ticks: {

                color:
                  '#8f9cae',

                precision:
                  0,

                font: {
                  size: 10
                }

              },

              grid: {
                color: '#edf1f5'
              },

              border: {
                display: false
              }

            }

          }

        }

      }

    )

}


/* =========================================
   REVENUE CHART
========================================= */

const createRevenueChart = () => {

  if (
    !revenueChartCanvas.value
  ) {

    return

  }


  if (revenueChart) {

    revenueChart.destroy()

    revenueChart =
      null

  }


  const labels =
    revenueOverTime.value.map(
      item =>
        item.label
    )


  const values =
    revenueOverTime.value.map(
      item =>
        Number(
          item.revenue || 0
        )
    )


  revenueChart =
    new Chart(
      revenueChartCanvas.value,
      {

        type: 'line',


        data: {

          labels,


          datasets: [

            {

              label:
                'Revenue',

              data:
                values,

              borderColor:
                '#42b995',

              backgroundColor:
                'rgba(66, 185, 149, 0.10)',

              fill:
                true,

              tension:
                0.35,

              borderWidth:
                2.5,

              pointRadius:
                3,

              pointHoverRadius:
                5,

              pointBackgroundColor:
                '#42b995'

            }

          ]

        },


        options: {

          responsive:
            true,

          maintainAspectRatio:
            false,


          plugins: {

            legend: {
              display: false
            },


            tooltip: {

              backgroundColor:
                '#172033',

              padding:
                10,


              callbacks: {

                label:
                  context =>
                    ` Revenue: ${formatCurrency(
                      context.raw
                    )}`

              }

            }

          },


          scales: {

            x: {

              grid: {
                display: false
              },

              border: {
                display: false
              },

              ticks: {

                color:
                  '#8f9cae',

                maxRotation:
                  0,

                autoSkip:
                  true,

                maxTicksLimit:
                  10,

                font: {
                  size: 10
                }

              }

            },


            y: {

              beginAtZero:
                true,

              ticks: {

                color:
                  '#8f9cae',

                font: {
                  size: 10
                },

                callback:
                  value =>
                    `₱${Number(
                      value
                    ).toLocaleString()}`

              },

              grid: {
                color: '#edf1f5'
              },

              border: {
                display: false
              }

            }

          }

        }

      }

    )

}


/* =========================================
   RESIZE
========================================= */

const handleResize = () => {

  if (ordersChart) {
    ordersChart.resize()
  }


  if (revenueChart) {
    revenueChart.resize()
  }

}


/* =========================================
   LIFECYCLE
========================================= */

onMounted(() => {

  /*
   * Show the most recent real report snapshot immediately.
   * Then refresh it silently from PostgreSQL.
   */
  loadCachedReports()

  loadAllReports({
    showLoading: false
  })

  window.addEventListener(
    'resize',
    handleResize
  )

})


onBeforeUnmount(() => {

  if (ordersChart) {

    ordersChart.destroy()

    ordersChart =
      null

  }


  if (revenueChart) {

    revenueChart.destroy()

    revenueChart =
      null

  }


  window.removeEventListener(
    'resize',
    handleResize
  )

})

</script>


<style scoped>

/* =========================================
   RESET
========================================= */

* {
  box-sizing: border-box;
}


/* =========================================
   PAGE
========================================= */

.dashboard {
  display: flex;

  width: 100%;
  min-height: 100vh;

  background: #f7f9fb;

  color: #172033;
}


/* =========================================
   SIDEBAR
========================================= */

.sidebar {
  position: fixed;

  top: 0;
  left: 0;
  bottom: 0;

  width: 218px;

  display: flex;
  flex-direction: column;

  background: #ffffff;

  border-right:
    1px solid #e4e9ef;

  z-index: 20;
}


.sidebar-logo {
  height: 84px;

  display: flex;
  align-items: center;

  padding: 0 22px;

  border-bottom:
    1px solid #e4e9ef;
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


/* =========================================
   SIDEBAR NAV
========================================= */

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


.sidebar-bottom {
  margin-top: auto;

  padding:
    15px
    10px
    20px;

  border-top:
    1px solid #e4e9ef;
}


.nav-item.logout {
  margin: 0;
}


/* =========================================
   MAIN
========================================= */

.main-content {
  width:
    calc(100% - 218px);

  min-height: 100vh;

  margin-left: 218px;
}


/* =========================================
   CONTENT
========================================= */

.content {
  padding:
    30px
    32px
    40px;
}


.page-header {
  margin-bottom: 21px;
}


.page-header h1 {
  margin: 0;

  color: #172033;

  font-size: 25px;

  line-height: 1.2;

  font-weight: 650;
}


.page-header p {
  margin:
    6px
    0
    0;

  color: #8290a3;

  font-size: 12px;
}


/* =========================================
   FILTER CARD
========================================= */

.filter-card {
  min-height: 70px;

  display: flex;

  align-items: center;

  justify-content: space-between;

  padding:
    13px
    18px;

  margin-bottom: 20px;

  background: #ffffff;

  border:
    1px solid #e3e9ef;

  border-radius: 11px;
}


.filter-label {
  color: #536680;

  font-size: 11px;

  font-weight: 600;
}


.date-controls {
  display: flex;

  align-items: flex-end;

  gap: 11px;
}


.date-input-group {
  display: flex;

  flex-direction: column;

  gap: 5px;
}


.date-input-group label {
  color: #8b98a8;

  font-size: 9px;

  font-weight: 600;
}


.date-input-group input {
  width: 140px;
  height: 32px;

  padding:
    0
    9px;

  border:
    1px solid #dfe6ed;

  border-radius: 7px;

  outline: none;

  background: #ffffff;

  color: #42536a;

  font-family: inherit;

  font-size: 10px;
}


.date-input-group input:focus {
  border-color: #8dbecb;

  box-shadow:
    0 0 0 3px
    rgba(
      36,
      127,
      153,
      0.07
    );
}


.date-separator {
  margin-bottom: 9px;

  color: #a0abba;

  font-size: 12px;
}


.apply-button {
  height: 32px;

  padding:
    0
    14px;

  border: none;

  border-radius: 7px;

  background: #247f99;

  color: #ffffff;

  font-family: inherit;

  font-size: 10px;

  font-weight: 600;

  cursor: pointer;
}


.apply-button:hover:not(:disabled) {
  background: #1d6e84;
}


.apply-button:disabled {
  opacity: 0.6;

  cursor: not-allowed;
}


/* =========================================
   TABS
========================================= */

.report-tabs {
  display: flex;

  gap: 24px;

  margin-bottom: 21px;

  border-bottom:
    1px solid #e3e9ef;
}


.report-tab {
  position: relative;

  padding:
    0
    0
    11px;

  border: none;

  background: transparent;

  color: #98a4b2;

  font-family: inherit;

  font-size: 11px;

  font-weight: 600;

  cursor: pointer;
}


.report-tab.active {
  color: #247f99;
}


.report-tab.active::after {
  content: "";

  position: absolute;

  left: 0;
  right: 0;
  bottom: -1px;

  height: 2px;

  background: #247f99;

  border-radius:
    2px
    2px
    0
    0;
}


/* =========================================
   SECTION HEADING
========================================= */

.section-heading {
  margin-bottom: 13px;

  color: #91a0b4;

  font-size: 10px;

  letter-spacing: 0.08em;

  font-weight: 700;
}


/* =========================================
   ERROR
========================================= */

.error-card {
  display: flex;

  gap: 10px;

  align-items: flex-start;

  padding:
    12px
    14px;

  margin-bottom: 17px;

  background: #fff7f7;

  border:
    1px solid #f1dddd;

  border-radius: 9px;
}


.error-icon {
  width: 22px;
  height: 22px;

  display: flex;

  align-items: center;
  justify-content: center;

  flex-shrink: 0;

  border-radius: 50%;

  background: #fbe2e2;

  color: #b54a4a;

  font-size: 11px;

  font-weight: 700;
}


.error-card strong {
  color: #7c3f3f;

  font-size: 11px;
}


.error-card p {
  margin:
    3px
    0
    0;

  color: #a46c6c;

  font-size: 10px;
}


/* =========================================
   FEEDBACK SUMMARY
========================================= */

.feedback-summary-grid {
  display: grid;

  grid-template-columns:
    repeat(
      3,
      minmax(0, 1fr)
    );

  gap: 14px;

  margin-bottom: 14px;
}


.feedback-summary-card {
  min-height: 139px;

  padding:
    17px
    19px;

  background: #ffffff;

  border:
    1px solid #e1e8ef;

  border-radius: 12px;

  box-shadow:
    0 1px 2px
    rgba(
      30,
      50,
      70,
      0.025
    );
}


.summary-card-top {
  display: flex;

  align-items: center;

  justify-content: space-between;
}


.summary-card-top > span {
  color: #647793;

  font-size: 11px;

  font-weight: 500;
}


.feedback-card-icon {
  width: 30px;
  height: 30px;

  display: flex;

  align-items: center;

  justify-content: center;

  border-radius: 8px;

  font-weight: 700;
}


.feedback-blue {
  background: #edf8fa;

  color: #1e7d99;
}


.feedback-yellow {
  background: #fff8e5;

  color: #ef9d00;
}


.feedback-red {
  background: #fff0f0;

  color: #ef5a5a;
}


.feedback-bubble {
  font-size: 19px;
}


.star-symbol {
  font-size: 19px;

  line-height: 1;
}


.summary-value {
  margin-top: 13px;

  color: #172033;

  font-size: 27px;

  line-height: 1;

  font-weight: 700;
}


.summary-description {
  margin-top: 9px;

  color: #9aa7b8;

  font-size: 10px;
}


.rating-value-row {
  display: flex;

  align-items: baseline;

  gap: 4px;
}


.rating-max {
  color: #9aa7b8;

  font-size: 13px;
}


.stars-row {
  display: flex;

  gap: 2px;

  margin-top: 9px;
}


.star {
  color: #d5dce4;

  font-size: 14px;
}


.star.filled {
  color: #f0a000;
}


/* =========================================
   FEEDBACK TABLE
========================================= */

.feedback-table-card {
  overflow: hidden;

  background: #ffffff;

  border:
    1px solid #e1e8ef;

  border-radius: 12px;

  box-shadow:
    0 1px 2px
    rgba(
      30,
      50,
      70,
      0.025
    );
}


.table-wrapper {
  width: 100%;

  overflow-x: auto;
}


table {
  width: 100%;

  border-collapse: collapse;

  table-layout: fixed;
}


thead tr {
  background: #fbfcfd;
}


th {
  padding:
    12px
    13px;

  border-bottom:
    1px solid #e5ebf0;

  color: #637793;

  font-size: 10px;

  font-weight: 600;

  text-align: left;
}


td {
  padding:
    11px
    13px;

  border-bottom:
    1px solid #edf1f4;

  color: #4b5d75;

  font-size: 10px;

  vertical-align: middle;
}


tbody tr:last-child td {
  border-bottom: none;
}


tbody tr:hover {
  background: #fcfeff;
}


/* =========================================
   TABLE WIDTHS
========================================= */

th:nth-child(1),
td:nth-child(1) {
  width: 13%;
}


th:nth-child(2),
td:nth-child(2) {
  width: 15%;
}


th:nth-child(3),
td:nth-child(3) {
  width: 13%;
}


th:nth-child(4),
td:nth-child(4) {
  width: 12%;
}


th:nth-child(5),
td:nth-child(5) {
  width: 22%;
}


th:nth-child(6),
td:nth-child(6) {
  width: 13%;
}


th:nth-child(7),
td:nth-child(7) {
  width: 12%;
}


.order-number-cell {
  color: #30445d;

  font-weight: 600;
}


.comment-cell {
  white-space: nowrap;

  overflow: hidden;

  text-overflow: ellipsis;
}


/* =========================================
   RATING
========================================= */

.rating-stars {
  display: flex;

  gap: 1px;

  white-space: nowrap;
}


.table-star {
  color: #d8dee6;

  font-size: 11px;
}


.table-star.filled {
  color: #f0a000;
}


/* =========================================
   TYPE PILLS
========================================= */

.type-pill {
  display: inline-flex;

  align-items: center;

  padding:
    5px
    8px;

  border-radius: 999px;

  font-size: 9px;

  line-height: 1;

  font-weight: 600;
}


.feedback-type {
  background: #edf8fa;

  color: #287692;
}


.complaint-type {
  background: #fff1f1;

  color: #f15e5e;
}


.suggestion-type {
  background: #f2edff;

  color: #7755e9;
}


/* =========================================
   VIEW
========================================= */

.view-button {
  display: inline-flex;

  align-items: center;

  gap: 5px;

  border: none;

  background: transparent;

  color: #247f99;

  font-family: inherit;

  font-size: 10px;

  font-weight: 600;

  cursor: pointer;
}


.view-button:hover {
  color: #175e71;
}


.view-icon {
  font-size: 9px;
}


/* =========================================
   EMPTY STATE
========================================= */

.empty-table-cell {
  padding:
    55px
    20px !important;

  text-align: center;
}


.empty-feedback {
  display: flex;

  align-items: center;

  justify-content: center;

  flex-direction: column;
}


.empty-feedback-icon {
  width: 43px;
  height: 43px;

  display: flex;

  align-items: center;

  justify-content: center;

  margin-bottom: 10px;

  border-radius: 50%;

  background: #edf8fa;

  font-size: 18px;
}


.empty-feedback strong {
  color: #43546a;

  font-size: 12px;
}


.empty-feedback p {
  margin:
    5px
    0
    0;

  color: #98a4b3;

  font-size: 10px;
}


/* =========================================
   TABLE SPINNER
========================================= */

.table-spinner {
  width: 24px;
  height: 24px;

  margin-bottom: 10px;

  border:
    3px solid #e7eef2;

  border-top-color:
    #247f99;

  border-radius: 50%;

  animation:
    spin
    0.8s
    linear
    infinite;
}


/* =========================================
   SALES SUMMARY
========================================= */

.sales-summary-grid {
  display: grid;

  grid-template-columns:
    repeat(
      5,
      minmax(0, 1fr)
    );

  gap: 13px;

  margin-bottom: 15px;
}


.sales-summary-card {
  min-height: 137px;

  padding:
    16px
    17px;

  background: #ffffff;

  border:
    1px solid #e1e8ef;

  border-radius: 12px;
}


.sales-icon,
.status-count-icon {
  width: 30px;
  height: 30px;

  display: flex;

  align-items: center;

  justify-content: center;

  border-radius: 8px;
}


.sales-icon img {
  width: 16px;
  height: 16px;

  object-fit: contain;
}


.blue-icon {
  background: #edf8fa;
}


.green-icon {
  background: #e9f7f0;
}


.purple-icon {
  background: #f0edfb;
}


.pending-icon {
  background: #fff5e3;

  color: #e29a00;

  font-size: 20px;
}


.ready-icon {
  background: #e9f7f0;

  color: #36a57d;

  font-size: 13px;

  font-weight: 700;
}


/* =========================================
   CHARTS
========================================= */

.charts-grid {
  display: grid;

  grid-template-columns:
    repeat(
      2,
      minmax(0, 1fr)
    );

  gap: 15px;
}


.chart-card {
  min-height: 340px;

  padding:
    18px
    19px;

  background: #ffffff;

  border:
    1px solid #e1e8ef;

  border-radius: 12px;
}


.chart-header {
  display: flex;

  align-items: flex-start;

  justify-content: space-between;

  gap: 10px;
}


.chart-header h2 {
  margin: 0;

  color: #31445b;

  font-size: 14px;

  font-weight: 600;
}


.chart-header p {
  margin:
    5px
    0
    0;

  color: #9aa6b4;

  font-size: 10px;
}


.chart-label {
  padding:
    5px
    8px;

  border-radius: 6px;

  font-size: 9px;

  font-weight: 700;
}


.orders-label {
  background: #edf8fa;

  color: #287692;
}


.revenue-label {
  background: #e9f7f0;

  color: #339875;
}


.chart-container {
  position: relative;

  height: 252px;

  margin-top: 18px;
}


/* =========================================
   FEEDBACK MODAL
========================================= */

.modal-overlay {
  position: fixed;

  inset: 0;

  z-index: 100;

  display: flex;

  align-items: center;

  justify-content: center;

  padding: 20px;

  background:
    rgba(
      23,
      32,
      51,
      0.25
    );

  backdrop-filter:
    blur(2px);
}


.feedback-modal {
  width:
    min(
      530px,
      100%
    );

  overflow: hidden;

  background: #ffffff;

  border-radius: 14px;

  box-shadow:
    0 18px 50px
    rgba(
      23,
      32,
      51,
      0.18
    );
}


.modal-header {
  display: flex;

  align-items: flex-start;

  justify-content: space-between;

  padding:
    20px
    21px;

  border-bottom:
    1px solid #edf1f4;
}


.modal-kicker {
  color: #91a0b4;

  font-size: 9px;

  letter-spacing: 0.08em;

  font-weight: 700;
}


.modal-header h2 {
  margin:
    5px
    0
    0;

  color: #23354a;

  font-size: 18px;

  font-weight: 650;
}


.modal-close {
  width: 30px;
  height: 30px;

  border: none;

  border-radius: 7px;

  background: #f5f7f9;

  color: #7e8a99;

  font-size: 21px;

  line-height: 1;

  cursor: pointer;
}


.modal-close:hover {
  background: #edf1f4;

  color: #4d5b6e;
}


.modal-body {
  padding:
    21px;
}


.feedback-detail-grid {
  display: grid;

  grid-template-columns:
    repeat(
      2,
      minmax(0, 1fr)
    );

  gap:
    16px
    14px;
}


.detail-item {
  display: flex;

  flex-direction: column;

  gap: 4px;
}


.detail-item span,
.detail-label {
  color: #97a3b1;

  font-size: 9px;

  font-weight: 600;
}


.detail-item strong {
  color: #33475e;

  font-size: 11px;

  font-weight: 600;
}


.modal-rating {
  margin-top: 20px;
}


.modal-stars {
  display: flex;

  align-items: center;

  gap: 2px;

  margin-top: 7px;
}


.modal-star {
  color: #d7dee6;

  font-size: 17px;
}


.modal-star.filled {
  color: #f0a000;
}


.modal-rating-number {
  margin-left: 5px;

  color: #667892;

  font-size: 11px;
}


.comment-section {
  margin-top: 20px;
}


.full-comment {
  min-height: 105px;

  margin-top: 8px;

  padding: 13px;

  border:
    1px solid #e4eaf0;

  border-radius: 9px;

  background: #fbfcfd;

  color: #4d5d73;

  font-size: 11px;

  line-height: 1.7;
}


.modal-footer {
  display: flex;

  justify-content: flex-end;

  padding:
    13px
    21px;

  border-top:
    1px solid #edf1f4;
}


.close-button {
  height: 34px;

  padding:
    0
    15px;

  border:
    1px solid #dce4eb;

  border-radius: 7px;

  background: #ffffff;

  color: #64758b;

  font-family: inherit;

  font-size: 10px;

  font-weight: 600;

  cursor: pointer;
}


.close-button:hover {
  background: #f7f9fb;
}


/* =========================================
   ANIMATION
========================================= */

@keyframes spin {

  to {
    transform: rotate(360deg);
  }

}


/* =========================================
   RESPONSIVE
========================================= */

@media (max-width: 1150px) {

  .sales-summary-grid {
    grid-template-columns:
      repeat(
        3,
        minmax(0, 1fr)
      );
  }


  .charts-grid {
    grid-template-columns: 1fr;
  }

}


@media (max-width: 850px) {

  .feedback-summary-grid {
    grid-template-columns:
      repeat(
        2,
        minmax(0, 1fr)
      );
  }


  .filter-card {
    flex-direction: column;

    align-items: flex-start;

    gap: 12px;
  }


  .date-controls {
    width: 100%;

    flex-wrap: wrap;
  }

}


@media (max-width: 620px) {

  .sidebar {
    width: 70px;
  }


  .sidebar-logo {
    justify-content: center;

    padding: 0;
  }


  .logo-text,
  .nav-item span {
    display: none;
  }


  .sidebar-nav {
    padding:
      20px
      8px;
  }


  .nav-item {
    justify-content: center;

    padding: 0;
  }


  .nav-item img {
    margin-right: 0;
  }


  .main-content {
    width:
      calc(100% - 70px);

    margin-left: 70px;
  }


  .content {
    padding:
      20px
      15px;
  }


  .feedback-summary-grid,
  .sales-summary-grid {
    grid-template-columns: 1fr;
  }


  .date-controls {
    align-items: flex-start;
  }


  .date-separator {
    display: none;
  }


  .date-input-group input {
    width: 150px;
  }


  .feedback-detail-grid {
    grid-template-columns: 1fr;
  }

}

</style>