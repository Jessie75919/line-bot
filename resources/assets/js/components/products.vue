<template>
	<div>
		<h2>Products</h2>
		<div class="card card-body mb-2" v-for="product in products" :key="product.id">
			<h3>{{ product.name }}</h3>
			<p>{{ product.description }}</p>
		</div>
	</div>
</template>
<script>
    export default {
        data() {
            return {
                products : [],
                product  : {}
            }
        },

        created(){
            this.fetchProducts();
        },

        methods : {
            fetchProducts(){
                fetch('api/v1/Product')
                    .then(res => res.json())
                    .then(res => this.products = res.data)
	                .then(() => this.showMeMeney())


            },

            showMeMeney(){
              console.log("$$$$$$$$$$$$$$$$");
            }
        }
    }

</script>
