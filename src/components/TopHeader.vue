<template>
  <header class="top-header">

    <div class="date">
      {{ currentDate }}
    </div>


    <div class="user-info">

      <div class="user-details">

        <strong>
          Admin
        </strong>

        <span>
          Shop Staff
        </span>

      </div>


      <div class="user-avatar">
        A
      </div>

    </div>

  </header>
</template>


<script setup>

import {
  computed
} from 'vue'


const currentDate = computed(() => {

  return new Date().toLocaleDateString(
    'en-PH',
    {
      weekday: 'long',
      month: 'short',
      day: 'numeric',
      year: 'numeric'
    }
  )

})

</script>


<style scoped>

/* =========================================
   SHARED ADMIN HEADER
   Matches Dashboard / Orders / Services
========================================= */

.top-header {
  width: 100%;
  height: 84px;

  display: flex;
  align-items: center;

  justify-content: space-between;

  flex-shrink: 0;

  padding: 0 32px;

  background: #ffffff;

  border-bottom:
    1px solid #e4e9ef;
}


/* =========================================
   DATE
========================================= */

.date {
  color: #64748b;

  font-size: 10px;

  font-weight: 400;

  white-space: nowrap;
}


/* =========================================
   STAFF PROFILE
========================================= */

.user-info {
  display: flex;

  align-items: center;

  gap: 10px;

  flex-shrink: 0;
}


.user-details {
  display: flex;

  flex-direction: column;

  align-items: flex-end;

  justify-content: center;
}


.user-details strong {
  color: #172033;

  font-size: 11px;

  font-weight: 600;

  line-height: 1.1;
}


.user-details span {
  margin-top: 2px;

  color: #8090a5;

  font-size: 9px;

  font-weight: 400;

  line-height: 1.1;
}


/* =========================================
   AVATAR
========================================= */

.user-avatar {
  width: 28px;
  height: 28px;

  display: flex;

  align-items: center;
  justify-content: center;

  flex-shrink: 0;

  border-radius: 50%;

  background: #dff3f7;

  color: #217f98;

  font-size: 11px;

  font-weight: 500;
}

:global(html) {
  overflow-y: scroll;
}

</style>