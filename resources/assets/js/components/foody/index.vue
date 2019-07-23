<template>
	<div>
		<header>
			<div class="collapse bg-dark" id="navbarHeader">
				<div class="container">
					<div class="row">
						<div class="col-sm-8 col-md-7 py-4">
							<h4 class="text-white">About</h4>
							<p class="text-muted">Just Restaurant Around</p>
						</div>

					</div>
				</div>
			</div>
			<div class="navbar navbar-dark bg-dark shadow-sm">
				<div class="container d-flex justify-content-between">
					<a class="navbar-brand d-flex align-items-center" href="#">
						<svg class="mr-2" fill="none" height="20" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
						     stroke-width="2" viewBox="0 0 24 24" width="20" xmlns="http://www.w3.org/2000/svg">
							<path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
							<circle cx="12" cy="13" r="4"></circle>
						</svg>
						<strong>Foody</strong>
					</a>
					<button aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler"
					        data-target="#navbarHeader"
					        data-toggle="collapse" type="button">
						<span class="navbar-toggler-icon"></span>
					</button>
				</div>
			</div>
		</header>

		<main role="main">
			<transition name="fade">
				<div class="album py-5 bg-light" v-if="shops">
					<div class="container">
						<div class="row">
							<div class="col-md-4" v-for="shop in shops">
								<div class="card mb-4 shadow-sm">
									<a :href="shop.url" target="_blank">
										<img :src="shop.photo_url"
										     class="card-img-top">
									</a>
									<div class="card-body">
										<p class="card-text"> {{ shop.label }} </p>
										<div class="d-flex justify-content-between align-items-center">
											<div class="btn-group">
												<a :href="shop.url" class="btn btn-sm btn-outline-secondary"
												   target="_blank"
												>Google Map</a>
												<a :href="shop.website" class="btn btn-sm btn-outline-secondary"
												   target="_blank"
												>Website</a>
											</div>
											<small class="text-muted">{{ shop.is_opening }}</small>
										</div>
									</div>
								</div>
							</div>

						</div>
					</div>
				</div>
			</transition>
		</main>
		<transition name="fade">
			<div class="loading" id="loading" v-if="isLoading">
				<div class="loadDotted"></div>
				<div class="loadDotted"></div>
				<div class="loadDotted"></div>
			</div>
		</transition>
	</div>
</template>

<script>
  import 'bootstrap/dist/css/bootstrap.min.css';
  import 'bootstrap/dist/js/bootstrap.min';

  export default {
    name: 'foody',
    data() {
      return {
        apiUrl: $('meta[name=\'api-url\']').val(),
        shops: null,
        isLoading: true
      };
    },
    methods: {},
    created() {
      navigator.geolocation.getCurrentPosition(showPosition);

      const vm = this;

      function showPosition(position) {
        const latitude  = position.coords.latitude;
        const longitude = position.coords.longitude;

        axios.post(`${vm.apiUrl}/api/shops`, { latitude, longitude })
          .then(({ data }) => {
            vm.shops = data.data;
            console.log(data.data);
          })
          .then(() => vm.isLoading = false);
      }
    }
  };
</script>

<style lang="scss" scoped>

	main {
		min-height: 900px;
	}

	.loading {
		display: block;
		position: fixed;
		width: 200px;
		height: 100px;
		top: 50%;
		left: 45%;
		margin: -50px 0 0 -50px;
		text-align: center;
		border-radius: 10px;
		z-index: 99999;
	}

	.loading:after {
		position: fixed;
		width: 100%;
		height: 100%;
		background-color: rgba(37, 60, 81, 0.8);
		top: 0;
		left: 0;
		content: "";
	}

	.loadDotted {
		position: relative;
		display: inline-block;
		width: 10px;
		height: 10px;
		border-radius: 50%;
		background-color: #ffffff;
		margin: 45px 10px 0 10px;
		animation: circle 1s ease 0s infinite;
		-webkit-animation: circle 1s ease 0s infinite;
		-moz-animation: circle 1s ease 0s infinite;
		-o-animation: circle 0.5s ease 0s infinite;
		z-index: 9999;
	}

	.loadDotted:nth-child(2) {
		animation-delay: 0.3s;
	}

	.loadDotted:nth-child(3) {
		animation-delay: 0.6s;
	}

	@keyframes circle {
		0% {
			transform: scale(0, 0);
		}
		50% {
			transform: scale(2, 2);
		}
		100% {
			transform: scale(1, 1);
		}
	}

	@-webkit-keyframes circle {
		0% {
			transform: scale(0, 0);
		}
		50% {
			transform: scale(1.3, 1.3);
		}
		100% {
			transform: scale(1, 1);
		}
	}

	@-moz-keyframes circle {
		0% {
			transform: scale(0, 0);
		}
		50% {
			transform: scale(1.3, 1.3);
		}
		100% {
			transform: scale(1, 1);
		}
	}

	@-o-keyframes circle {
		0% {
			transform: scale(0, 0);
		}
		50% {
			transform: scale(1.3, 1.3);
		}
		100% {
			transform: scale(1, 1);
		}
	}

	.fade-enter-active, .fade-leave-active {
		transition: opacity .5s;
	}

	.fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */
	{
		opacity: 0;
	}

</style>