<template>
  <section
    class="html-block-section py-12 md:py-16 lg:py-20"
    :class="renderedData.css_classes"
  >
    <div class="container mx-auto px-4">
      <!-- Section Title (if provided) -->
      <div v-if="section.title" class="text-center mb-8 md:mb-12">
        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
          {{ section.title }}
        </h2>
      </div>

      <!-- Custom HTML Content -->
      <div
        v-if="renderedData.html_content"
        class="html-content"
        v-html="sanitizedHtml"
      ></div>

      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <p class="text-gray-500">No content available</p>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: 'HtmlBlockSection',
  props: {
    section: {
      type: Object,
      required: true,
    },
  },
  computed: {
    renderedData() {
      return this.section.rendered_data || {};
    },
    sanitizedHtml() {
      // Basic sanitization - in production, use a library like DOMPurify
      const html = this.renderedData.html_content || '';
      
      // Remove potentially dangerous tags and attributes
      // This is a basic implementation - for production use DOMPurify
      const tempDiv = document.createElement('div');
      tempDiv.innerHTML = html;
      
      // Remove script tags
      const scripts = tempDiv.querySelectorAll('script');
      scripts.forEach(script => script.remove());
      
      // Remove event handlers
      const allElements = tempDiv.querySelectorAll('*');
      allElements.forEach(element => {
        // Remove on* attributes (onclick, onload, etc.)
        Array.from(element.attributes).forEach(attr => {
          if (attr.name.startsWith('on')) {
            element.removeAttribute(attr.name);
          }
        });
        
        // Remove javascript: hrefs
        if (element.hasAttribute('href')) {
          const href = element.getAttribute('href');
          if (href && href.toLowerCase().startsWith('javascript:')) {
            element.removeAttribute('href');
          }
        }
      });
      
      return tempDiv.innerHTML;
    },
  },
};
</script>

<style scoped>
/* Scoped styles for HTML content */
.html-content {
  /* Reset some common styles to prevent conflicts */
  line-height: 1.6;
  word-wrap: break-word;
  overflow-wrap: break-word;
}

/* Style common HTML elements within the content */
.html-content :deep(h1) {
  @apply text-2xl sm:text-3xl md:text-4xl font-bold mb-3 sm:mb-4 text-gray-900;
}

.html-content :deep(h2) {
  @apply text-xl sm:text-2xl md:text-3xl font-bold mb-2 sm:mb-3 text-gray-900;
}

.html-content :deep(h3) {
  @apply text-lg sm:text-xl md:text-2xl font-bold mb-2 text-gray-900;
}

.html-content :deep(h4) {
  @apply text-base sm:text-lg md:text-xl font-bold mb-2 text-gray-900;
}

.html-content :deep(p) {
  @apply mb-3 sm:mb-4 text-sm sm:text-base text-gray-700;
}

.html-content :deep(a) {
  @apply text-blue-600 hover:text-blue-800 underline break-words;
}

.html-content :deep(ul) {
  @apply list-disc list-inside mb-3 sm:mb-4 text-sm sm:text-base text-gray-700;
}

.html-content :deep(ol) {
  @apply list-decimal list-inside mb-3 sm:mb-4 text-sm sm:text-base text-gray-700;
}

.html-content :deep(li) {
  @apply mb-1 sm:mb-2;
}

.html-content :deep(img) {
  @apply max-w-full h-auto rounded-lg my-3 sm:my-4;
}

.html-content :deep(blockquote) {
  @apply border-l-4 border-blue-600 pl-3 sm:pl-4 italic text-sm sm:text-base text-gray-700 my-3 sm:my-4;
}

.html-content :deep(code) {
  @apply bg-gray-100 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded text-xs sm:text-sm font-mono;
}

.html-content :deep(pre) {
  @apply bg-gray-100 p-3 sm:p-4 rounded-lg overflow-x-auto mb-3 sm:mb-4 text-xs sm:text-sm;
}

.html-content :deep(table) {
  @apply w-full border-collapse mb-3 sm:mb-4 text-xs sm:text-sm;
}

.html-content :deep(th) {
  @apply bg-gray-100 border border-gray-300 px-2 sm:px-4 py-1 sm:py-2 text-left font-semibold;
}

.html-content :deep(td) {
  @apply border border-gray-300 px-2 sm:px-4 py-1 sm:py-2;
}
</style>
