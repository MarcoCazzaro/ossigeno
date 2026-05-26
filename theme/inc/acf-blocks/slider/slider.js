/**
 * Slider block Alpine.js functionality
 */
document.addEventListener('alpine:init', () => {
	Alpine.data('sliderData', () => ({
		activeSlide: 0,
		slides: [],
		lightboxOpen: false,
		lightboxSlide: 0,
		lightboxImages: [],
		keyboardListenerAdded: false,

		init() {
			// Remove hidden class from lightbox overlay on frontend (Alpine.js is loaded)
			const lightboxOverlay =
				document.querySelectorAll('.lightbox-overlay');
			if (lightboxOverlay) {
				lightboxOverlay.forEach((overlay) => {
					overlay.classList.remove('hidden');
				});
			}

			// Check if we have a slides container reference
			if (this.$refs.slidesContainer) {
				// Get all slides from the referenced container, but only if they have class 'slide'
				this.slides = Array.from(
					this.$refs.slidesContainer.children
				).filter((child) => {
					return child.classList.contains('slide');
				});

				// Setup slides with initial scale and click handling
				this.slides.forEach((slide, index) => {
					// Find the slide image element
					const slideImage = slide.querySelector('.slide-image');
					if (slideImage) {
						// Set initial scale class - active slides get scale-125
						if (index !== this.activeSlide) {
							slideImage
								.querySelector('img')
								.classList.remove('scale-125');
						} else {
							slideImage
								.querySelector('img')
								.classList.add('scale-125');
						}
					}

					// Add click handler to each slide
					slide.addEventListener('click', (e) => {
						// Navigate to the link instead of activating the slide
						const url = slide.dataset.linkUrl;
						const target = slide.dataset.linkTarget;

						if (url.trim() !== '') {
							if (target === '_blank') {
								window.open(url, '_blank');
							} else {
								window.location.href = url;
							}
						} else {
							this.activateSlide(index);
							this.lightBox(index);
						}
					});
				});
			}
		},

		// Method to activate a slide when clicked
		activateSlide(index) {
			if (!this.slides.length || index === this.activeSlide) return;

			// Find and update the slide image opacities using classes
			const previousSlideImage =
				this.slides[this.activeSlide].querySelector('.slide-image');
			if (previousSlideImage) {
				previousSlideImage
					.querySelector('img')
					.classList.remove('scale-125');
			}

			// Update active slide index
			this.activeSlide = index;

			// Update new active slide scale
			const newActiveSlideImage =
				this.slides[this.activeSlide].querySelector('.slide-image');
			if (newActiveSlideImage) {
				newActiveSlideImage
					.querySelector('img')
					.classList.add('scale-125');
			}

			// Scroll slide into view with smooth behavior
			this.slides[index].scrollIntoView({
				behavior: 'smooth',
				block: 'nearest',
				inline: 'center',
			});
		},

		nextSlide() {
			if (!this.slides.length) return;
			const nextIndex =
				this.activeSlide === this.slides.length - 1
					? 0
					: this.activeSlide + 1;
			this.activateSlide(nextIndex);
		},

		prevSlide() {
			if (!this.slides.length) return;
			const prevIndex =
				this.activeSlide === 0
					? this.slides.length - 1
					: this.activeSlide - 1;
			this.activateSlide(prevIndex);
		},

		lightBox(index) {
			// Don't open lightbox in WordPress admin
			if (document.body.classList.contains('wp-admin')) {
				return;
			}

			// Set the current lightbox slide
			this.lightboxSlide = index;

			// Create lightbox images array from slides
			this.lightboxImages = this.slides.map((slide) => {
				const img = slide.querySelector('img');
				return {
					src: img ? img.src : '',
					alt: img ? img.alt : '',
					title: slide.dataset.title || '',
				};
			});

			// Preload current, previous and next images
			this.preloadLightboxImages();

			// Open lightbox
			this.lightboxOpen = true;
			document.body.classList.add('overflow-hidden');

			// Add keyboard navigation if not already added
			if (!this.keyboardListenerAdded) {
				this.addKeyboardNavigation();
				this.keyboardListenerAdded = true;
			}
		},

		closeLightbox() {
			this.lightboxOpen = false;
			document.body.classList.remove('overflow-hidden');
		},

		nextLightboxSlide() {
			const nextIndex =
				(this.lightboxSlide + 1) % this.lightboxImages.length;
			this.lightboxSlide = nextIndex;
			this.preloadLightboxImages();
		},

		prevLightboxSlide() {
			const prevIndex =
				this.lightboxSlide === 0
					? this.lightboxImages.length - 1
					: this.lightboxSlide - 1;
			this.lightboxSlide = prevIndex;
			this.preloadLightboxImages();
		},

		preloadLightboxImages() {
			// Preload current, previous and next images
			const indicesToPreload = [
				this.lightboxSlide,
				(this.lightboxSlide + 1) % this.lightboxImages.length,
				this.lightboxSlide === 0
					? this.lightboxImages.length - 1
					: this.lightboxSlide - 1,
			];

			indicesToPreload.forEach((index) => {
				if (
					this.lightboxImages[index] &&
					this.lightboxImages[index].src
				) {
					const img = new Image();
					img.src = this.lightboxImages[index].src;
				}
			});
		},

		addKeyboardNavigation() {
			// Add keyboard navigation
			document.addEventListener('keydown', (e) => {
				if (this.lightboxOpen) {
					switch (e.key) {
						case 'ArrowLeft':
							e.preventDefault();
							this.prevLightboxSlide();
							break;
						case 'ArrowRight':
							e.preventDefault();
							this.nextLightboxSlide();
							break;
						case 'Escape':
							e.preventDefault();
							this.closeLightbox();
							break;
					}
				}
			});
		},
	}));
});
