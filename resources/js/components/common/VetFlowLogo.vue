<script setup>
defineProps({
  size: {
    type: [Number, String],
    default: 44
  },

  showSubtitle: {
    type: Boolean,
    default: true
  }
})
</script>

<template>
  <div class="vetflow-logo">
    <div
      class="logo-symbol"
      :style="{ width: `${size}px`, height: `${size}px` }"
    >
      <!-- Coração -->
      <svg
        viewBox="0 0 48 48"
        xmlns="http://www.w3.org/2000/svg"
        aria-hidden="true"
      >
        <path
          d="M24 42C21.5 39.8 10 31.4 6.5 24.3C2.7 16.7 7.1 9 14.4 9C18.6 9 21.5 11.5 24 14.5C26.5 11.5 29.4 9 33.6 9C40.9 9 45.3 16.7 41.5 24.3C38 31.4 26.5 39.8 24 42Z"
          fill="currentColor"
        />

        <!-- Patinha centralizada -->
      <g transform="translate(0 2)">
      <circle cx="18" cy="19" r="2.4" fill="white" />
      <circle cx="23" cy="16.5" r="2.4" fill="white" />
      <circle cx="28" cy="18" r="2.4" fill="white" />
      <circle cx="31" cy="22" r="2.2" fill="white" />

    <path
    d="M17.5 27.5C17.5 23.8 20.4 21.5 24 21.5C27.6 21.5 30.5 23.8 30.5 27.5C30.5 30.2 28.5 32 26.2 31.2C24.8 30.7 23.2 30.7 21.8 31.2C19.5 32 17.5 30.2 17.5 27.5Z"
    fill="white"
       />
     </g>
    </svg>
    </div>

    <div class="logo-text">
      <div class="logo-name">
        Vet<span>Flow</span>
      </div>

      <small v-if="showSubtitle">
        Conectando pets e profissionais
      </small>
    </div>
  </div>
</template>

<style scoped>
.vetflow-logo {
  display: inline-flex;
  align-items: center;
  gap: 9px;
}

.logo-symbol {
  flex-shrink: 0;
  color: #009b72;
}

.logo-symbol svg {
  display: block;
  width: 100%;
  height: 100%;
}

.logo-text {
  display: flex;
  flex-direction: column;
}

.logo-name {
  color: #0b2942;
  font-size: 25px;
  font-weight: 700;
  line-height: 1;
}

.logo-name span {
  color: #009b72;
}

.logo-text small {
  margin-top: 4px;

  color: #718096;
  font-size: 9px;
  line-height: 1;
  white-space: nowrap;
}
</style>