<template>
  <div class="product-list-container">
    <div v-if="loading" class="loading-state">
      <i class="fas fa-spinner fa-spin"></i> جاري التحميل...
    </div>
    
    <div v-else-if="products.length === 0" class="empty-state">
      <i class="fas fa-box-open"></i>
      <p>لا توجد منتجات.</p>
    </div>

    <div v-else class="products-grid">
      <div 
        v-for="product in products" 
        :key="product.id" 
        class="product-card"
        @click="$emit('add-to-cart', product)"
      >
        <div class="product-image">
          <img v-if="product.img && product.img !== 'placeholder.png' && !product.img.includes('no_img.jpg')" :src="product.img" :alt="product.name" />
          <img v-else src="/images/default_product.png" :alt="product.name" />
        </div>
        <div class="product-info">
          <h3>{{ product.name }}</h3>
          <span class="barcode text-muted" style="font-size: 12px;"><i class="fas fa-barcode"></i> {{ product.barcode || 'بدون باركود' }}</span>
          <div class="product-price">{{ Number(product.price).toFixed(2) }} SAR</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  products: {
    type: Array,
    required: true
  },
  loading: {
    type: Boolean,
    default: false
  }
});
defineEmits(['add-to-cart']);
</script>

<style scoped>
.product-list-container {
  flex: 1;
  overflow-y: auto;
  padding-bottom: 20px;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  gap: 20px;
}

.product-card {
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(5px);
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0,0,0,0.05);
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid rgba(255,255,255,0.2);
}

.dark-mode .product-card {
  background: rgba(40, 40, 40, 0.7);
  border: 1px solid rgba(255,255,255,0.05);
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.product-image {
  position: relative;
  height: 140px;
  background: #f8f9fa;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}

.product-image i {
  font-size: 40px;
  color: #ccc;
}

.dark-mode .product-image { background: #1a1a1a; }

.product-image img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  transition: transform 0.3s;
}

.product-card:hover .product-image img {
  transform: scale(1.05);
}

.product-price {
  font-weight: bold;
  color: #2ecc71;
  margin-top: 5px;
}

.product-info {
  padding: 12px;
}

.product-info h3 {
  margin: 0 0 5px 0;
  font-size: 15px;
  font-weight: 600;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.product-info .barcode {
  display: block;
  margin-bottom: 5px;
}

.loading-state, .empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  color: #888;
  font-size: 18px;
}
.loading-state i, .empty-state i {
  font-size: 40px;
  margin-bottom: 15px;
  color: #ccc;
}
</style>
