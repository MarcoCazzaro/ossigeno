/**
 * Splash Cover block functionality
 */
document.addEventListener('DOMContentLoaded', function () {
	/**
	 * Loads a background video for a single container
	 * @param {HTMLElement} container - The video container element
	 * @param {string} videoUrl - URL of the video to load
	 * @param {HTMLElement} titlesWrapper - The titles wrapper element to show after video loads
	 */
	const loadBackgroundVideo = (container, videoUrl, titlesWrapper) => {
		// If no video URL is provided, skip this container
		if (!videoUrl) {
			console.warn('No video URL provided for a video container');
			return;
		}

		// Create video element
		const videoElement = document.createElement('video');

		// Find the progress elements for this container
		const progressElement = container.querySelector(
			'.progress .progress-bar'
		);

		// Initialize progress bar width
		if (progressElement) {
			progressElement.style.width = '0%';
		}

		// Show progress indicator after delay
		setTimeout(() => {
			if (container) {
				container.classList.remove('opacity-0');
				container.classList.add('opacity-100');
			}
		}, 500);

		// Fetch the video with progress tracking
		fetch(videoUrl)
			.then((response) => {
				if (!response.ok) {
					throw new Error(`HTTP error! Status: ${response.status}`);
				}

				const contentLength = response.headers.get('content-length');
				let loaded = 0;

				// Only set up progress tracking if we have both contentLength and progressElement
				if (contentLength && progressElement) {
					return new Response(
						new ReadableStream({
							start(controller) {
								const reader = response.body.getReader();
								read();
								function read() {
									reader.read().then((progressEvent) => {
										if (progressEvent.done) {
											controller.close();
											return;
										}
										loaded +=
											progressEvent.value.byteLength;
										let currentProgress = Math.round(
											(loaded / contentLength) * 100
										);
										progressElement.style.width =
											currentProgress + '%';
										controller.enqueue(progressEvent.value);
										read();
									});
								}
							},
						})
					);
				} else {
					// If we can't track progress, just return the response
					return response;
				}
			})
			.then((response) => response.blob())
			.then((blob) => {
				// Set up the video element
				videoElement.src = URL.createObjectURL(blob);
				videoElement.autoplay = true;
				videoElement.loop = true;
				videoElement.muted = true;
				videoElement.playsInline = true;
				videoElement.classList.add(
					'w-full',
					'h-full',
					'object-cover',
					'mt-0',
					'mb-0',
					'transition-opacity',
					'duration-300',
					'opacity-0'
				);

				videoElement.addEventListener('loadeddata', () => {
					setTimeout(() => {
						videoElement.classList.remove('opacity-0');
						videoElement.classList.add('opacity-100');
					}, 50); // A small delay after loading
				});

				// Clear the container and append the video
				container.innerHTML = '';
				container.appendChild(videoElement);

				// Show titles wrapper if it exists
				if (titlesWrapper) {
					titlesWrapper.classList.remove('opacity-0');
				}
			})
			.catch((error) => {
				console.error('Error loading video:', error);
				// Display error message in container
				container.innerHTML =
					'<p class="text-center p-4">Error loading video</p>';
			});
	};

	// Initialize all splash cover elements
	const initBackgroundVideos = () => {
		// Get device type
		const isMobile = window.innerWidth < 768;

		// Find all splash cover elements
		const splashCovers = document.querySelectorAll('.ssnail-splash-cover');

		// Process each splash cover element
		splashCovers.forEach((splashCover) => {
			// Find the video container within this splash cover
			const videoContainer = splashCover.querySelector(
				'.ssnail-splash-cover__video-container[data-video-url-desktop], .ssnail-splash-cover__video-container[data-video-url-mobile]'
			);

			// Find the titles wrapper within this splash cover
			const titlesWrapper = splashCover.querySelector(
				'.ssnail-titles-wrapper'
			);

			// If no video container or no video URLs, show titles immediately if they exist
			if (
				!videoContainer ||
				(!videoContainer.dataset.videoUrlDesktop &&
					!videoContainer.dataset.videoUrlMobile)
			) {
				// No video to load, show titles immediately if they exist
				if (titlesWrapper) {
					titlesWrapper.classList.remove('opacity-0');
				}
			} else {
				// Has video, load it and show titles after loading
				let videoUrls = isMobile
					? videoContainer.dataset.videoUrlMobile
					: videoContainer.dataset.videoUrlDesktop;

				// Split by semicolon and pick a random video URL
				let videoUrl = '';
				if (videoUrls) {
					const urlArray = videoUrls
						.split(';')
						.map((url) => url.trim())
						.filter((url) => url.length > 0);
					if (urlArray.length > 0) {
						const randomIndex = Math.floor(
							Math.random() * urlArray.length
						);
						videoUrl = urlArray[randomIndex];
					}
				}

				// Only proceed if we have a valid URL
				if (videoUrl) {
					loadBackgroundVideo(
						videoContainer,
						videoUrl,
						titlesWrapper
					);
				} else {
					if (titlesWrapper) {
						titlesWrapper.classList.remove('opacity-0');
					}
				}
			}
		});
	};

	// Run initialization
	initBackgroundVideos();
});
