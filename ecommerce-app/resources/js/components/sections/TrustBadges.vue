<template>
  <div class="py-6 bg-gray-50 border-t border-gray-100">
    <div class="container mx-auto px-4">
      <div class="flex flex-wrap items-center justify-center gap-6 md:gap-10 opacity-60 hover:opacity-80 transition-opacity duration-300">
        <!-- SSL Secure -->
        <div class="flex items-center gap-2 text-gray-500">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
          </svg>
          <span class="text-xs font-medium">SSL Seguro</span>
        </div>

        <!-- Visa -->
        <div class="flex items-center text-gray-500">
          <svg class="h-8 w-auto" viewBox="0 0 780 500" fill="currentColor">
            <path d="M293.2 348.7l33.4-195.8h53.5l-33.4 195.8h-53.5zM518.6 157.4c-10.6-4-27.2-8.3-47.9-8.3-52.8 0-90 26.6-90.2 64.7-.3 28.2 26.5 43.9 46.8 53.3 20.8 9.6 27.8 15.8 27.7 24.4-.1 13.2-16.6 19.2-32 19.2-21.4 0-32.7-3-50.3-10.2l-6.9-3.1-7.5 43.9c12.5 5.5 35.6 10.2 59.6 10.5 56.2 0 92.7-26.3 93.1-67-0.2-22.3-14-39.3-44.8-53.3-18.7-9.1-30.1-15.1-30-24.3 0-8.1 9.7-16.8 30.6-16.8 17.4-0.3 30.1 3.5 39.9 7.5l4.8 2.3 7.1-42.8z"/>
            <path d="M620.4 152.9h-41.3c-12.8 0-22.4 3.5-28 16.3l-79.3 179.5h56.1s9.2-24.1 11.2-29.4h68.5c1.6 6.9 6.5 29.4 6.5 29.4h49.6l-43.3-195.8zm-65.9 126.4c4.4-11.3 21.4-54.8 21.4-54.8-.3.5 4.4-11.4 7.1-18.8l3.6 17s10.3 47 12.4 56.6h-44.5z"/>
            <path d="M247.2 152.9L194.8 282l-5.6-27.2c-9.8-31.4-40.3-65.5-74.4-82.5l47.8 174.2h56.5l84.1-193.6h-56z"/>
            <path d="M146.9 152.9H59.4l-.7 3.8c67 16.2 111.3 55.4 129.7 102.5l-18.7-89.8c-3.2-12.4-12.7-16-23.8-16.5z"/>
          </svg>
        </div>

        <!-- Mastercard -->
        <div class="flex items-center text-gray-500">
          <svg class="h-8 w-auto" viewBox="0 0 780 500" fill="currentColor">
            <circle cx="312" cy="250" r="150" opacity="0.6"/>
            <circle cx="468" cy="250" r="150" opacity="0.6"/>
          </svg>
          <span class="text-xs font-medium ml-1">Mastercard</span>
        </div>

        <!-- PayPal -->
        <div class="flex items-center gap-1 text-gray-500">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M7.076 21.337H2.47a.641.641 0 01-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106z"/>
          </svg>
          <span class="text-xs font-medium">PayPal</span>
        </div>

        <!-- Garantía -->
        <div class="flex items-center gap-2 text-gray-500">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
          </svg>
          <span class="text-xs font-medium">Garantía</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TrustBadges',
};
</script>
