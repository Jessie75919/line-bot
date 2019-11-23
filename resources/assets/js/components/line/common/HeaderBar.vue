<template>
	<nav class="navbar navbar-expand-sm navbar-dark bg-dark">
		<a class="navbar-brand" href="#">Give Me Lighter!</a>
		<button aria-controls="navbarSupportedContent" aria-expanded="false"
		        aria-label="Toggle navigation"
		        class="navbar-toggler"
		        data-target="#navbarSupportedContent"
		        data-toggle="collapse" type="button">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav mr-auto">
				<li :class="{active: page === 'index'}" class="nav-item"
				    data-target=".navbar-collapse.show" data-toggle="collapse">
					<a @click="setPage('index')"
					   class="nav-link"
					>記錄<span class="sr-only">(current)</span></a>
				</li>
				<li :class="{active: page === 'setting'}" class="nav-item"
				    data-target=".navbar-collapse.show" data-toggle="collapse">
					<a @click="setPage('setting')"
					   class="nav-link"
					>設定<span class="sr-only"></span></a>
				</li>
			</ul>
		</div>
	</nav>
</template>

<script>
  export default {
    name: 'header-bar',
    props: {
      page: {
        required: true,
        default: 'index'
      }
    },
    methods: {
      setPage(page) {
        this.$emit('setPage', page);
      }
    }
  };
</script>

<style scoped>

</style>