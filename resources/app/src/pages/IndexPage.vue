<template>
  <q-page>
    <section id="hero-section" class="q-pt-none">
      <q-carousel v-model="slide" vertical transition-prev="slide-down" transition-next="slide-up" animated
        control-color="white" navigation navigation-icon="radio_button_unchecked"
        navigation-active-icon="radio_button_checked" :navigation-position="$q.platform.is.desktop ? 'left' : 'right'"
        arrows :height="$q.platform.is.mobile ? '100vh' : '650px'" class="text-white shadow-1">
        <q-carousel-slide :name="1" class="column no-wrap flex-center" style="position: relative; overflow: hidden">
          <!-- Black Background -->
          <div style="
              position: absolute;
              top: 0;
              left: 0;
              width: 100%;
              height: 100%;
              background: black;
              z-index: 0;
            "></div>

          <!-- Heart Background Image -->
          <div style="
              position: absolute;
              top: 50%;
              left: 50%;
              transform: translate(-50%, -50%);
              width: 100%;
              height: 100%;
              z-index: 1;
              pointer-events: none;
              opacity: 0.3;
              background-image: url('/images/Coração Fundos.png');
              background-size: contain;
              background-position: center;
              background-repeat: no-repeat;
            "></div>

          <div style="
              position: absolute;
              top: 0;
              left: 0;
              width: 100%;
              height: 100%;
              z-index: 2;
              pointer-events: none;
            ">
            <!-- Fundo cinza escuro como fallback -->
            <div v-if="!landingImages.hero_video || !landingImages.hero_video.trim() || !videoLoaded" :style="{
              position: 'absolute',
              top: 0,
              left: 0,
              width: '100%',
              height: '100%',
              background: '#2a2a2a',
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'center',
            }">
              <q-spinner-dots v-if="landingImages.hero_video && landingImages.hero_video.trim() && !videoLoaded" size="50px" color="white" style="position: relative; z-index: 2" />
            </div>

            <!-- Vídeo Hero -->
            <video
              v-if="landingImages.hero_video && landingImages.hero_video.trim()"
              autoplay
              muted
              loop
              playsinline
              preload="metadata"
              @loadeddata="onVideoLoad"
              @canplay="videoStarted = true"
              :style="{
                position: 'absolute',
                top: '50%',
                left: '50%',
                width: $q.platform.is.mobile ? 'max(100%, 177.78vh)' : '140%',
                height: $q.platform.is.mobile ? 'max(100%, 56.25vw)' : '140%',
                minWidth: '100%',
                minHeight: '100%',
                transform: 'translate(-50%, -50%)',
                objectFit: 'cover',
                pointerEvents: 'none',
                filter: 'brightness(0.5)',
                opacity: videoStarted ? 1 : 0,
                transition: 'opacity 0.5s ease',
              }"
            >
              <source :src="getHeroVideoUrl()" type="video/mp4">
              Seu navegador não suporta vídeos HTML5.
            </video>
          </div>
          <div style="position: relative; z-index: 3">
            <div
              :class="`row ${$q.platform.is.desktop ? 'text-h2' : 'hero-mobile-text'} justify-center items-center q-px-xl`">
              <div class="col-12 col-md-6">
                <span :class="`${$q.platform.is.desktop ? 'text-h2' : 'hero-mobile-title'} text-bold`">{{
                  t('hero.title1') }}</span>
                <span :class="`${$q.platform.is.desktop ? 'text-h2' : 'hero-mobile-title'} text-weight-light`">{{
                  t('hero.title2') }}</span>
                <div class="row q-gutter-md q-mt-md-lg q-mt-sm">
                  <div class="col-2" v-if="$q.platform.is.desktop">
                    <hr />
                  </div>
                  <div class="col-12 col-md-7">
                    <q-btn rounded outline color="white" text-color="white" :size="$q.platform.is.desktop ? 'lg' : 'md'"
                      :label="buttonLabel" to="produtos" no-caps class="hover-btn" />
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6 text-right gt-sm">
                <div>
                  <q-btn color="white" text-color="black" class="q-mt-md q-mb-md"
                    icon="img:https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/instagram.svg" round />
                </div>
                <div>
                  <q-btn color="white" text-color="black" class="q-mt-md q-mb-md"
                    icon="img:https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/facebook.svg" round />
                </div>
              </div>
            </div>
          </div>
        </q-carousel-slide>
      </q-carousel>
    </section>

    <section id="info" class="container">
      <div :class="`row justify-center q-py-xl ${$q.platform.is.desktop ? 'q-col-gutter-xl' : ''}`">
        <div class="col-12 col-sm-6 col-md-3">
          <q-card flat class="rounded-borders" style="border: 1px solid black">
            <q-card-section class="text-h6">
              <div class="row">
                <div class="col-2 col-sm-1 flex flex-center">
                  <q-avatar color="white" :style="avatarStyle">
                    <q-icon name="warehouse" size="32px" />
                  </q-avatar>
                </div>
                <div class="col-10 col-sm-11 text-weight-light">{{ t('info.delivery') }}</div>
              </div>
            </q-card-section>
          </q-card>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
          <q-card flat class="rounded-borders">
            <q-card-section class="text-h6">
              <div class="row">
                <div class="col-2 col-sm-1 flex flex-center">
                  <q-avatar color="white" :style="avatarStyle">
                    <q-icon name="factory" size="32px" />
                  </q-avatar>
                </div>
                <div class="col-10 col-sm-11 text-weight-light">{{ t('info.structure') }}</div>
              </div>
            </q-card-section>
          </q-card>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
          <q-card flat class="rounded-borders" style="border: 1px solid black">
            <q-card-section class="text-h6">
              <div class="row">
                <div class="col-2 col-sm-1 flex flex-center">
                  <q-avatar color="white" :style="avatarStyle">
                    <q-icon name="local_shipping" size="32px" />
                  </q-avatar>
                </div>
                <div class="col-10 col-sm-11 text-weight-light">{{ t('info.distribution') }}</div>
              </div>
            </q-card-section>
          </q-card>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
          <q-card flat class="rounded-borders">
            <q-card-section class="text-h6">
              <div class="row">
                <div class="col-2 col-sm-1 flex flex-center">
                  <q-avatar color="white" :style="avatarStyle">
                    <q-icon name="verified" size="32px" />
                  </q-avatar>
                </div>
                <div class="col-10 col-sm-11 text-weight-light">{{ t('info.quality') }}</div>
              </div>
            </q-card-section>
          </q-card>
        </div>
      </div>
    </section>

    <section id="filter" :class="`container ${$q.platform.is.desktop ? 'q-mt-xl' : 'q-mt-xs'}`"
      style="position: relative; overflow: hidden">
      <!-- Animated Fabric Elements -->
      <div class="fabric-animation-container">
        <div class="floating-thread thread-1"></div>
        <div class="floating-thread thread-2"></div>
        <div class="floating-thread thread-3"></div>
        <div class="floating-yarn yarn-1"></div>
        <div class="floating-yarn yarn-2"></div>
        <div class="floating-pattern pattern-1"></div>
        <div class="floating-pattern pattern-2"></div>
        <div class="floating-pattern pattern-3"></div>
      </div>

      <div class="row q-col-gutter-md" style="position: relative; z-index: 2">
        <div class="col-12 col-md-6">
          <q-card flat class="filter-card cursor-pointer clickable-card"
            :style="landingImages.lancamentos ? { backgroundImage: `url(${landingImages.lancamentos})` } : { backgroundColor: '#e0e0e0' }"
            @click="$router.push('/produtos?categoria=LANÇAMENTOS')">
            <q-card-section class="flex flex-center" :style="cardHeight">
              <div class="row text-center text-white">
                <div class="col-12 text-h6 text-weight-light">
                  {{ t('filter.releases.subtitle') }}
                </div>
                <div class="col-12 text-h3 text-bold">{{ t('filter.releases.title') }}</div>
                <div class="col-12 q-mt-md">
                  <q-btn rounded outline color="white" text-color="white" label="Ver Lançamentos" no-caps
                    class="hover-btn" />
                </div>
              </div>
            </q-card-section>
          </q-card>
        </div>
        <div class="col-12 col-md-6">
          <q-card flat class="filter-card border-radius-left cursor-pointer clickable-card"
            :style="landingImages.produtos ? { backgroundImage: `url(${landingImages.produtos})` } : { backgroundColor: '#e0e0e0' }"
            @click="$router.push('/produtos')">
            <q-card-section class="flex flex-center" :style="cardHeight">
              <div class="row text-center text-white">
                <div class="col-12 text-h6 text-weight-light">
                  {{ t('filter.products.subtitle') }}
                </div>
                <div class="col-12 text-h3 text-bold">{{ t('filter.products.title') }}</div>
                <div class="col-12 q-mt-md">
                  <q-btn rounded outline color="white" text-color="white" label="Ver Produtos" no-caps
                    class="hover-btn" />
                </div>
              </div>
            </q-card-section>
          </q-card>
        </div>
        <div class="col-12 col-md-6">
          <q-card flat class="filter-card-3 border-radius-right palette-card-hover"
            :style="landingImages.cartela ? { backgroundImage: `url(${landingImages.cartela})` } : { backgroundColor: '#e0e0e0' }">
            <q-card-section class="flex flex-center" :style="cardHeight">
              <div class="row text-center text-white">
                <div class="col-12 text-h6 text-weight-light">
                  {{ t('filter.colors.subtitle') }}
                </div>
                <div class="col-12 text-h3 text-bold">{{ t('filter.colors.title') }}</div>
                <div class="col-12 q-mt-md">
                  <div class="row q-gutter-sm justify-center">
                    <q-btn v-for="palette in palettes" :key="palette.id" rounded outline color="white"
                      text-color="white" :label="`Cartela ${palette.name}`" no-caps class="hover-btn palette-btn"
                      @click="openColorPaletteGalleryData(palette.name)" />
                  </div>
                </div>
              </div>
            </q-card-section>
          </q-card>
        </div>
        <div class="col-12 col-md-6">
          <q-card flat class="filter-card-4 cursor-pointer clickable-card" @click="openInstagram"
            :style="landingImages.instagram ? { backgroundImage: `url(${landingImages.instagram})` } : { backgroundColor: '#e0e0e0' }">
            <q-card-section class="flex flex-center" :style="cardHeight">
              <div class="row text-center text-white">
                <div class="col-12 text-h6 text-weight-light">
                  {{ t('filter.instagram.subtitle') }}
                </div>
                <div class="col-12 text-h3 text-bold">{{ t('filter.instagram.title') }}</div>
                <div class="col-12 q-mt-md">
                  <q-btn rounded outline color="white" text-color="white" label="Seguir Instagram" no-caps
                    class="hover-btn" icon="launch" />
                </div>
              </div>
            </q-card-section>
          </q-card>
        </div>
      </div>
    </section>

    <section id="products" class="container" style="margin-top: 50px; position: relative">
      <div class="square-effects se-left"></div>
      <div class="row q-py-lg">
        <div class="col-12 q-pt-xl">
          <div class="col-12 text-h5" style="letter-spacing: 4px">{{ t('products.subtitle') }}</div>
          <div class="col-12 text-h1 text-bold">{{ t('products.title') }}</div>
        </div>
        <div class="col-12">
          <Carousel :items-to-show="getItemsToShow()" :items-to-scroll="1" :wrap-around="true" :transition="500"
            v-model="currentSlide" ref="carouselRef">
            <Slide v-for="(product, index) in products" :key="index">
              <div class="column items-center q-ma-md shadow-3" style="width: 100%; cursor: pointer"
                @click="$router.push(product.path)">
                <q-img :src="product.image" style="width: 100%; aspect-ratio: 1/1" fit="cover" :alt="product.name"
                  loading="lazy" />
                <div class="row q-pa-md bg-white full-width">
                  <div class="col-10">
                    <div class="text-uppercase text-grey-7">
                      {{ product.firstProductName }}
                    </div>
                    <div class="text-h6 sora-bold">{{ product.title }}</div>
                  </div>
                  <div class="col-2">
                    <q-btn icon="add" padding="xs" color="primary" />
                  </div>
                </div>
              </div>
            </Slide>

            <template #addons>
              <CarouselPagination />
            </template>
          </Carousel>

          <div v-if="$q.platform.is.desktop" class="row justify-center q-mt-md q-gutter-sm"
            style="position: absolute; right: 50px; top: 50%">
            <q-btn v-if="$q.platform.is.desktop" round color="black" icon="arrow_forward" @click="next(carouselRef)" />
          </div>
        </div>
      </div>
    </section>

    <section v-if="campaign" id="release" class="container"
      :style="`margin-top: ${$q.platform.is.desktop ? '200px' : '100px'}; background-color: #4c5044`">
      <div class="row justify-center q-col-gutter-lg">
        <div class="col-12 col-md-4">
          <div class="square-1 flex flex-center q-pa-xl">
            <div class="row">
              <div class="col-12 text-h6 text-weight-regular">{{ campaignText }}</div>

              <div class="col-12 text-h1 text-bold" style="white-space: pre-line;">{{ launchText }}</div>

              <div class="col-12 bg-white q-py-md q-px-xl" style="color: #292d41; margin: 20px 0">
                <div class="text-h4 text-weight-bolder">{{ campaignTitle }}</div>
                <div class="text-h4">{{ campaignSeason }}</div>
              </div>

              <div class="col-12">
                <q-btn flat no-caps class="text-h5">
                  {{ campaignButton }}
                  <q-avatar size="md" icon="arrow_forward" color="white" text-color="black" class="q-ml-md" />
                </q-btn>
              </div>
            </div>
          </div>
        </div>

        <div :class="`col-12 col-md-6 q-py-lg ${$q.platform.is.desktop ? '' : 'q-mt-xl'}`">
          <q-video v-if="videoLoaded2 && campaign.youtube_url" :src="campaign.youtube_url"
            style="width: 100%; min-height: 300px; height: 100%; object-fit: cover" />
          <div v-else class="q-mb-lg bg-grey-3 flex flex-center" style="width: 100%; height: 350px">
            <q-spinner-dots size="40px" color="primary" />
          </div>
        </div>
      </div>
    </section>

    <section id="testimony" class="testimonials-section"
      :style="`padding: ${$q.platform.is.desktop ? '120px 0' : '80px 0'}; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); position: relative`">
      <!-- Background decorative elements -->
      <div class="testimonials-bg-decoration"></div>

      <div class="container">
        <!-- Section Header -->
        <div class="row justify-center q-mb-xl">
          <div class="col-12 text-center">
            <div class="text-overline text-primary q-mb-sm" style="letter-spacing: 3px; font-weight: 600">
              O QUE DIZEM SOBRE NÓS
            </div>
            <div class="text-h2 text-bold text-grey-9 q-mb-md">
              {{ t('testimonials.title') }}
            </div>
            <div class="testimonials-divider"></div>
          </div>
        </div>

        <!-- Loading State -->
        <div v-if="isLoadingReviews" class="row justify-center q-py-xl">
          <div class="col-auto text-center">
            <q-spinner-dots size="60px" color="primary" />
            <div class="text-h6 text-grey-6 q-mt-md">{{ t('testimonials.loading') }}</div>
          </div>
        </div>

        <!-- Testimonials Carousel -->
        <div v-else class="testimonials-carousel-container">
          <q-carousel v-model="testimony" transition-prev="slide-right" transition-next="slide-left" swipeable animated
            control-color="primary" padding arrows flat class="testimonials-carousel" :autoplay="5000"
            :slides-to-show="1" :slides-to-scroll="1" height="auto">
            <q-carousel-slide v-for="(group, idx) in getReviewGroups()" :key="'review-group-' + idx" :name="idx">
              <!-- Desktop Layout -->
              <div v-if="$q.platform.is.desktop" class="row q-col-gutter-lg">
                <div v-for="review in group" :key="review.id" class="col-6">
                  <div class="testimonial-card">
                    <!-- Header: Quote Icon and Stars -->
                    <div class="testimonial-header">
                      <div class="quote-icon">
                        <q-icon name="format_quote" size="32px" color="primary" />
                      </div>
                      <div class="testimonial-rating">
                        <q-icon v-for="star in 5" :key="star" name="star"
                          :color="star <= review.rating ? 'amber-5' : 'grey-4'" size="18px" />
                      </div>
                    </div>

                    <!-- Review Text -->
                    <div class="testimonial-text">"{{ review.text }}"</div>

                    <!-- Author Info -->
                    <div class="testimonial-author">
                      <q-avatar size="50px" class="testimonial-avatar">
                        <img :src="review.profilePhoto" :alt="review.name"
                          @error="$event.target.src = 'https://cdn.quasar.dev/img/boy-avatar.png'" />
                      </q-avatar>
                      <div class="author-info">
                        <div class="author-name">{{ review.name }}</div>
                        <div class="author-source">
                          {{ t('testimonials.rating', { source: review.source }) }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Mobile Layout -->
              <div v-else class="row justify-center">
                <div v-for="review in group" :key="review.id" class="col-12">
                  <div class="testimonial-card mobile">
                    <!-- Header: Quote Icon and Stars -->
                    <div class="testimonial-header">
                      <div class="quote-icon">
                        <q-icon name="format_quote" size="28px" color="primary" />
                      </div>
                      <div class="testimonial-rating">
                        <q-icon v-for="star in 5" :key="star" name="star"
                          :color="star <= review.rating ? 'amber-5' : 'grey-4'" size="16px" />
                      </div>
                    </div>

                    <!-- Review Text -->
                    <div class="testimonial-text mobile">"{{ review.text }}"</div>

                    <!-- Author Info -->
                    <div class="testimonial-author mobile">
                      <q-avatar size="40px" class="testimonial-avatar">
                        <img :src="review.profilePhoto" :alt="review.name"
                          @error="$event.target.src = 'https://cdn.quasar.dev/img/boy-avatar.png'" />
                      </q-avatar>
                      <div class="author-info">
                        <div class="author-name">{{ review.name }}</div>
                        <div class="author-source">
                          {{ t('testimonials.rating', { source: review.source }) }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </q-carousel-slide>
          </q-carousel>
        </div>
      </div>
    </section>

    <section id="about" class="container" :style="`margin-top: ${$q.platform.is.desktop ? '100px' : '50px'}`">
      <div :class="$q.platform.is.desktop ? 'q-pa-lg' : ''">
        <!-- Seção Sobre a Empresa -->
        <div class="row justify-center q-mb-xl">
          <div class="col-12 col-md-6">
            <div class="full-width">
              <q-img v-if="landingImages.prancheta1" :src="landingImages.prancheta1" lazy loading="lazy" />
              <div v-else class="bg-grey-3" style="width: 100%; height: 400px; display: flex; align-items: center; justify-content: center;">
                <q-icon name="image" size="64px" color="grey-5" />
              </div>
            </div>
          </div>
          <div class="col-12 col-md-4 q-pl-md">
            <div class="text-h6 text-grey-6 text-uppercase q-mb-sm">
              {{ t('about.company.subtitle') }}
            </div>
            <div :class="`text-h2 text-bold text-grey-9 ${$q.platform.is.desktop ? 'q-mb-lg' : 'q-mb-sm'}`">
              {{ t('about.company.title') }}
            </div>
            <div class="text-h6 text-grey-7" style="line-height: 1.6">
              <div class="row q-col-gutter-md items-center">
                <div class="col-2" v-if="$q.platform.is.desktop">
                  <hr />
                </div>
                <div class="col">
                  {{ t('about.company.description') }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Seção Mais de 130 Produtos -->
        <div class="row justify-center">
          <div :class="`col-12 col-md-4 ${$q.platform.is.desktop ? 'q-pr-md' : 'q-pl-md'} q-mb-md`">
            <div class="text-h6 text-grey-6 text-right text-uppercase q-mb-sm">
              {{ t('about.products.subtitle') }}
            </div>
            <div :class="`text-h2 text-bold text-grey-9 text-right ${$q.platform.is.desktop ? 'q-mb-lg' : 'q-mb-sm'}`">
              {{ t('about.products.title') }}
            </div>
            <div class="text-h6 text-grey-7" style="line-height: 1.6">
              <div class="row q-col-gutter-md items-center">
                <div class="col-2" v-if="$q.platform.is.desktop">
                  <hr />
                </div>
                <div class="col">
                  {{ t('about.products.description') }}
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-6">
            <q-img v-if="landingImages.prancheta2" :src="landingImages.prancheta2" lazy loading="lazy" />
            <div v-else class="bg-grey-3" style="width: 100%; height: 400px; display: flex; align-items: center; justify-content: center;">
              <q-icon name="image" size="64px" color="grey-5" />
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="certifications" class="container"
      :style="`margin: ${$q.platform.is.desktop ? '150px' : '50px'} 0; position: relative`">
      <div class="square-effects se-left"></div>
      <div class="row q-py-lg justify-center">
        <div class="col-12">
          <div :class="`col-12 ${$q.platform.is.desktop ? 'text-h1' : 'text-h2'} text-bold q-mt-xl`"
            :style="$q.platform.is.desktop ? 'margin-left: 150px' : ''">
            {{ t('certifications.title') }} <br />
            <span class="text-weight-light">{{ t('certifications.and') }}</span>
            {{ t('certifications.technology') }}
          </div>
        </div>

        <div class="col-12 col-md-10 justify-center">
          <div class="row items-center full-width">
            <div class="row items-center full-width" style="min-height: 250px">
              <q-btn v-if="$q.platform.is.desktop" round icon="arrow_back" color="black"
                @click="prev(carouselCertificationsRef)" class="q-mr-md" />
              <div style="flex: 1; min-width: 0; display: flex; align-items: center">
                <Carousel :items-to-show="getItemsToShow()" :items-to-scroll="1" :wrap-around="true" :transition="500"
                  :autoplay="3000" v-model="currentSlideCertifications" ref="carouselCertificationsRef"
                  style="width: 100%">
                  <Slide v-for="(certification, index) in certifications" :key="index">
                    <div class="column items-center q-ma-md shadow-3"
                      :style="{
                        width: certification.vertical ? '50px' : '100%',
                        height: certification.vertical ? '300px' : 'auto',
                      }">
                      <div :style="{
                        width: '100%',
                        height: '100%' ,
                        transform: certification.vertical ? 'rotate(90deg)' : 'none'
                      }">
                        <q-img :src="certification.image"
                          style="width: 100%; height: 100%"
                          position="center" />
                      </div>
                    </div>
                  </Slide>

                  <template #addons>
                    <CarouselPagination />
                  </template>
                </Carousel>
              </div>
              <q-btn v-if="$q.platform.is.desktop" round icon="arrow_forward" color="black"
                @click="next(carouselCertificationsRef)" class="q-ml-md" />
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Color Palette Gallery Dialog -->
    <q-dialog v-model="showColorPaletteDialog" maximized transition-show="slide-up" transition-hide="slide-down">
      <q-card class="gallery-dialog bg-dark text-white">
        <!-- Header -->
        <q-card-section class="row items-center q-pa-lg bg-dark">
          <div class="column">
            <div class="text-h4 text-bold text-white">CARTELA DE CORES</div>
            <div class="text-subtitle1 text-grey-5">Coleção {{ selectedPaletteYear }}</div>
            <div class="text-caption text-grey-6 q-mt-xs">
              {{ filteredColorPaletteImagesData.length }} cores
            </div>
          </div>
          <q-space />
          <q-input v-model="colorSearchFilter" outlined rounded dense :placeholder="t('ui.filterColors')" dark
            class="q-mr-md" style="min-width: 200px">
            <template v-slot:prepend>
              <q-icon name="search" color="white" />
            </template>
          </q-input>
          <q-btn icon="close" flat round v-close-popup color="white" size="lg" />
        </q-card-section>

        <!-- Masonry Gallery Grid -->
        <q-card-section class="gallery-masonry-container q-pa-lg">
          <div class="masonry-grid">
            <div v-for="(image, idx) in filteredColorPaletteImagesData" :key="idx" class="masonry-item"
              @click="openImageLightboxWithColor(image)">
              <q-img :src="image" fit="cover" class="masonry-image" loading="lazy">
                <div class="absolute-full gallery-overlay">
                  <q-icon name="zoom_in" size="lg" color="white" class="absolute-center" />
                </div>
              </q-img>
              <div class="color-name-label" :class="{ 'has-description': getColorDescriptionData(image) }">
                {{ getColorNameData(image) }}
              </div>
              <div v-if="getColorDescriptionData(image)" class="color-description-label">
                {{ getColorDescriptionData(image) }}
              </div>
            </div>
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- Image Lightbox -->
    <q-dialog v-model="showLightbox" maximized @keyup.esc="showLightbox = false">
      <q-card class="bg-black">
        <q-btn icon="close" flat round v-close-popup color="white" size="lg" class="absolute-top-right q-ma-lg"
          style="z-index: 1000" />

        <div class="column items-center justify-center" style="height: 100vh; padding: 80px 20px 20px 20px;">
          <!-- Nome da Cor -->
          <div v-if="selectedColorData" class="text-center q-mb-md">
            <div class="text-h4 text-white text-weight-bold">
              {{ selectedColorData.ref ? `${selectedColorData.ref} - ${selectedColorData.name}` : selectedColorData.name }}
            </div>
            <!-- Descrição da Cor -->
            <div v-if="selectedColorData.description" class="text-body1 text-grey-4 q-mt-sm" style="max-width: 600px; margin: 0 auto;">
              {{ selectedColorData.description }}
            </div>
          </div>

          <!-- Imagem -->
          <q-img v-if="lightboxImage" :src="lightboxImage" fit="contain" class="lightbox-image"
            style="max-height: 70vh; width: auto; cursor: pointer;"
            @click="showLightbox = false" />
        </div>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>
import { defineComponent, ref, computed, onMounted } from 'vue'
import { useQuasar } from 'quasar'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import { Carousel, Slide, Pagination as CarouselPagination } from 'vue3-carousel'
import 'vue3-carousel/carousel.css'
import { api } from 'src/boot/axios'

export default defineComponent({
  name: 'IndexPage',
  components: {
    Carousel,
    Slide,
    CarouselPagination,
  },
  setup() {
    const $q = useQuasar()
    const { t } = useI18n()
    const $router = useRouter()

    const slide = ref(1)
    const lorem = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'

    const currentSlide = ref(0)
    const carouselRef = ref(null)
    const currentSlideCertifications = ref(0)
    const carouselCertificationsRef = ref(null)

    // Landing page images (loaded from database only)
    const landingImages = ref({
      lancamentos: null,
      produtos: null,
      cartela: null,
      instagram: null,
      prancheta1: null,
      prancheta2: null,
      hero_video: null
    })

    // Load landing page images from database
    const loadLandingImages = async () => {
      try {
        const response = await api.get('/landing-page-images')
        const loadedImages = {}
        // Get backend URL (remove /api/v1 from the base URL)
        const backendUrl = api.defaults.baseURL.replace('/api/v1', '') || 'http://localhost'

        if (response.data && response.data.length) {
          response.data.forEach(img => {
            if (img.type && img.path) {
              // Para vídeo hero, armazenar o path (será processado por getHeroVideoUrl)
              if (img.type === 'hero_video') {
                landingImages.value[img.type] = img.path
              } else if (img.path) {
                // Para imagens, usar o path completo do banco
                landingImages.value[img.type] = `${backendUrl}/${img.path}`
              }
              loadedImages[img.type] = img.filename || img.path
            }
          })
          console.log('🖼️ Landing Images/Video Loaded:', landingImages.value)
        } else {
          console.warn('⚠️ No landing images found in database')
        }
      } catch (error) {
        console.error('❌ Failed to load landing images:', error)
      }
    }

    const getHeroVideoUrl = () => {
      if (!landingImages.value.hero_video) return null

      const path = landingImages.value.hero_video

      // Se já for uma URL completa (http/https), retornar diretamente
      if (path.startsWith('http://') || path.startsWith('https://')) {
        return path
      }

      // Caso contrário, construir URL do backend
      const backendUrl = api.defaults.baseURL.replace('/api/v1', '') || 'http://localhost'
      return `${backendUrl}/${path}`
    }

    // Load images on mount
    onMounted(() => {
      loadLandingImages()
    })

    const next = (carousel) => {
      const ref = carousel
      if (ref) {
        ref.next()
      }
    }
    const prev = (carousel) => {
      const ref = carousel
      if (ref) {
        ref.prev()
      }
    }

    const testimony = ref(0)
    const videoLoaded = ref(false)
    const videoLoaded2 = ref(false)
    const videoStarted = ref(false)

    // Carrega vídeos depois de 2 segundos para melhorar performance inicial
    setTimeout(() => {
      videoLoaded.value = true
      videoLoaded2.value = true
    }, 2000)

    // Função para detectar quando o vídeo carregou
    const onVideoLoad = () => {
      // Aguarda um pouco mais para garantir que o vídeo começou a tocar
      setTimeout(() => {
        videoStarted.value = true
      }, 1000)
    }

    // Dados das avaliações do Google (será preenchido via API)
    const googleReviews = ref([
      {
        id: 1,
        name: 'Carla Santos',
        profilePhoto: 'https://lh3.googleusercontent.com/a/default-user=s40-c',
        rating: 5,
        text: 'Excelente atendimento da PEMGIR! Produtos de qualidade superior e entrega rápida. A variedade de malhas é impressionante e a equipe sempre muito prestativa.',
        date: '2024-01-15',
        source: 'Google',
      },
      {
        id: 2,
        name: 'Roberto Silva',
        profilePhoto: 'https://lh3.googleusercontent.com/a/default-user=s40-c',
        rating: 5,
        text: 'Trabalho com a PEMGIR há anos e sempre me surpreendem com a qualidade e agilidade. Recomendo para todos os confeccionistas da região.',
        date: '2024-01-10',
        source: 'Google',
      },
      {
        id: 3,
        name: 'Ana Paula Oliveira',
        profilePhoto: 'https://lh3.googleusercontent.com/a/default-user=s40-c',
        rating: 5,
        text: 'Empresa confiável com produtos de primeira qualidade. A PEMGIR sempre cumpre os prazos e tem um atendimento diferenciado. Muito satisfeita!',
        date: '2024-01-05',
        source: 'Google',
      },
      {
        id: 4,
        name: 'João Carlos',
        profilePhoto: 'https://lh3.googleusercontent.com/a/default-user=s40-c',
        rating: 5,
        text: 'Estrutura industrial impressionante e variedade de produtos que atende todas as necessidades. A PEMGIR é referência no mercado têxtil.',
        date: '2023-12-28',
        source: 'Google',
      },
    ])

    const isLoadingReviews = ref(false)

    // Função para buscar avaliações do Google Places API
    const fetchGoogleReviews = async () => {
      isLoadingReviews.value = true
      try {
        // Place ID da Pemgir Malhas: ChIJOeX0M2Y3390QjneamLa7sDk
        const placeId = 'ChIJOeX0M2Y3390QjneamLa7sDk'
        const apiKey = process.env.GOOGLE_PLACES_API_KEY

        if (!apiKey) {
          console.warn('Google Places API Key não configurada. Usando dados de exemplo.')
          return
        }

        const response = await fetch(
          `https://maps.googleapis.com/maps/api/place/details/json?place_id=${placeId}&fields=reviews&key=${apiKey}`,
        )

        if (response.ok) {
          const data = await response.json()
          if (data.result && data.result.reviews) {
            googleReviews.value = data.result.reviews.map((review, index) => ({
              id: index + 1,
              name: review.author_name,
              profilePhoto:
                review.profile_photo_url ||
                'https://lh3.googleusercontent.com/a/default-user=s40-c',
              rating: review.rating,
              text: review.text,
              date: new Date(review.time * 1000).toISOString().split('T')[0],
              source: 'Google',
            }))
          }
        }
      } catch (error) {
        console.error('Erro ao buscar avaliações:', error)
      } finally {
        isLoadingReviews.value = false
      }
    }

    // Busca avaliações ao montar o componente
    fetchGoogleReviews()

    // Campanha ativa
    const campaign = ref(null)
    const currentLocale = computed(() => {
      const locale = t('locale')
      if (locale === 'pt-BR') return 'pt'
      if (locale === 'en-US') return 'en'
      if (locale === 'es-ES') return 'es'
      return 'pt'
    })

    // Computed properties para os textos da campanha
    const campaignText = computed(() => campaign.value?.[`campaign_text_${currentLocale.value}`] || '')
    const launchText = computed(() => {
      const text = campaign.value?.[`launch_text_${currentLocale.value}`] || ''
      // Converter \n escapado em quebra de linha real
      return text.replace(/\\n/g, '\n')
    })
    const campaignTitle = computed(() => campaign.value?.[`title_${currentLocale.value}`] || '')
    const campaignSeason = computed(() => campaign.value?.[`season_${currentLocale.value}`] || '')
    const campaignButton = computed(() => campaign.value?.[`button_text_${currentLocale.value}`] || '')

    // Função para carregar a campanha ativa
    const loadCampaign = async () => {
      try {
        const response = await api.get('/campaign/active')
        if (response.data) {
          campaign.value = response.data
        }
      } catch (error) {
        console.error('Erro ao carregar campanha:', error)
      }
    }

    // Carregar campanha ao montar o componente
    onMounted(() => {
      loadCampaign()
    })

    // Produtos por categoria (vindos da API)
    const products = ref([])

    // Função helper para limpar o path da imagem (remove 'storage/' se existir)
    const cleanImagePath = (path) => {
      if (!path) return null
      return path.replace(/^storage\//, '')
    }

    // Função para carregar produtos em destaque por categoria
    const loadFeaturedProducts = async () => {
      try {
        const response = await api.get('/categories')
        const categories = response.data

        products.value = categories
          .filter(cat => cat.featured_product && cat.featured_product.images && cat.featured_product.images.length > 0)
          .map((category) => ({
            title: category.name,
            name: category.name,
            firstProductName: category.featured_product.reference || category.featured_product.name,
            image: `${process.env.API_URL_IMG}/${cleanImagePath(category.featured_product.images[0].path)}`,
            path: `/produtos?categoria=${encodeURIComponent(category.name)}`,
          }))

        if (products.value.length === 0) {
          $q.notify({
            message: 'Nenhum produto em destaque encontrado na API',
            color: 'warning',
            position: 'top',
            timeout: 10000,
            icon: 'warning'
          })
        }
      } catch (error) {
        console.error('Erro ao carregar produtos em destaque:', error)
        $q.notify({
          message: 'Erro ao carregar produtos em destaque da API',
          caption: `Usuário: ${error.response?.data?.user || 'Desconhecido'}`,
          color: 'negative',
          position: 'top',
          timeout: 10000,
          icon: 'error'
        })
        products.value = []
      }
    }

    const certifications = ref([
      {
        image: '/certificacoes/2.jpg',
        vertical: true,
      },
      {
        image: '/certificacoes/5.jpg',
      },
      {
        image: '/certificacoes/7.jpg',
      },
      {
        image: '/certificacoes/8.jpg',
      },
    ])

    const getItemsToShow = () => {
      if ($q.screen.xs) return 1
      if ($q.screen.sm) return 2
      if ($q.screen.md) return 3
      return 4
    }

    const getImageStyle = (url) => {
      return {
        backgroundImage: `url(${url})`,
        backgroundSize: 'cover',
        backgroundPosition: 'center',
        height: $q.screen.xs ? '300px' : '450px',
        width: '100%',
      }
    }

    // Computed properties para responsividade
    const isXs = computed(() => $q.screen.xs)
    const isGtXs = computed(() => $q.screen.gt.xs)
    const buttonLabel = computed(() => ($q.screen.xs ? t('hero.buttonMobile') : t('hero.button')))
    const avatarStyle = computed(() => ($q.screen.xs ? '' : 'margin-left: -60px;'))
    const cardHeight = computed(() =>
      $q.screen.xs
        ? 'height: 250px; background-color: "transparent";'
        : 'height: 400px; background-color: "transparent";',
    )
    const imageStyle1 = computed(() =>
      $q.screen.xs ? 'height: 300px; border-radius: 50px;' : 'height: 450px; border-radius: 100px;',
    )

    // Função para formatar data
    const formatDate = (dateString) => {
      const date = new Date(dateString)
      return date.toLocaleDateString('pt-BR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      })
    }

    const toggleLeftDrawer = ref(false)

    // Agrupa reviews de 2 em 2 para o carousel
    function getReviewGroups() {
      const groups = []
      const isDesktop = $q.platform.is.desktop
      const step = isDesktop ? 2 : 1
      for (let i = 0; i < googleReviews.value.length; i += step) {
        groups.push(googleReviews.value.slice(i, i + step))
      }
      return groups
    }

    const scrollTo = (id, offset = 0) => {
      const el = document.getElementById(id)
      if (el) {
        const y = el.getBoundingClientRect().top + window.pageYOffset + offset
        window.scrollTo({ top: y, behavior: 'smooth' })
      }
    }

    const navigateToSection = (section) => {
      // Se estivermos na página de produtos, primeiro navegar para home
      if ($router.currentRoute.value.path !== '/') {
        $router.push('/').then(() => {
          // Aguarda um pequeno delay para a página carregar
          setTimeout(() => {
            scrollTo(section, section === 'testimony' ? -100 : 0)
          }, 100)
        })
      } else {
        // Se já estivermos na home, apenas fazer scroll
        scrollTo(section, section === 'testimony' ? -100 : 0)
      }
    }

    // Color Palette Gallery
    const showColorPaletteDialog = ref(false)
    const showLightbox = ref(false)
    const lightboxImage = ref('')
    const selectedColorData = ref(null)
    const colorSearchFilter = ref('')
    const selectedPaletteYear = ref('2026')

    const openImageLightbox = (imageUrl) => {
      lightboxImage.value = imageUrl
      showLightbox.value = true
    }

    const openInstagram = () => {
      window.open('https://www.instagram.com/pemgir_malhas/', '_blank')
    }

    return {
      slide,
      lorem,
      currentSlide,
      carouselRef,
      currentSlideCertifications,
      carouselCertificationsRef,
      testimony,
      products,
      certifications,
      videoLoaded,
      videoLoaded2,
      videoStarted,
      onVideoLoad,
      googleReviews,
      isLoadingReviews,
      toggleLeftDrawer,
      landingImages,
      getHeroVideoUrl,
      $q,

      // Computed properties
      isXs,
      isGtXs,
      buttonLabel,
      avatarStyle,
      cardHeight,
      imageStyle1,

      // Functions
      getItemsToShow,
      getImageStyle,
      formatDate,
      fetchGoogleReviews,
      next,
      prev,
      t,
      getReviewGroups,
      scrollTo,
      navigateToSection,

      // Color palette gallery
      showColorPaletteDialog,
      showLightbox,
      lightboxImage,
      selectedColorData,
      colorSearchFilter,
      selectedPaletteYear,
      openImageLightbox,
      openInstagram,
      loadFeaturedProducts,

      // Campaign data
      campaign,
      campaignText,
      launchText,
      campaignTitle,
      campaignSeason,
      campaignButton,
    }
  },

  data() {
    return {
      palettes: [],
      colors: [],
      loadingColors: false,
      selectedPaletteId: null,
    }
  },

  computed: {
    currentColorPaletteImagesData() {
      return this.colors.map(color => `${process.env.API_URL_IMG}/${color.image}`)
    },

    filteredColorPaletteImagesData() {
      if (!this.colorSearchFilter) {
        return this.currentColorPaletteImagesData
      }

      const searchTerm = this.colorSearchFilter.toLowerCase()
      const filtered = this.colors.filter(color =>
        color.name.toLowerCase().includes(searchTerm) ||
        (color.ref && color.ref.toLowerCase().includes(searchTerm))
      )
      return filtered.map(color => `${process.env.API_URL_IMG}/${color.image}`)
    }
  },

  mounted() {
    this.loadPalettes()
    this.loadFeaturedProducts()
  },

  methods: {
    async loadPalettes() {
      try {
        const response = await api.get('/palettes', { params: { active: 1, with_colors: 1 } })
        this.palettes = response.data

        // Definir primeira paleta como selecionada
        if (this.palettes.length > 0) {
          this.selectedPaletteYear = this.palettes[0].name
          this.selectedPaletteId = this.palettes[0].id
        }
      } catch (error) {
        console.error('Erro ao carregar paletas:', error)
      }
    },

    async loadColors(paletteId) {
      this.loadingColors = true
      try {
        const response = await api.get('/colors', { params: { palette_id: paletteId } })
        this.colors = response.data
      } catch (error) {
        console.error('Erro ao carregar cores:', error)
      } finally {
        this.loadingColors = false
      }
    },

    async openColorPaletteGalleryData(year) {
      const palette = this.palettes.find(p => p.name === year)
      if (palette) {
        this.selectedPaletteYear = year
        this.selectedPaletteId = palette.id
        await this.loadColors(palette.id)
        this.showColorPaletteDialog = true
      }
    },

    getColorNameData(imagePath) {
      const imageUrl = imagePath.split('/').pop()
      const color = this.colors.find(c => c.image && c.image.includes(imageUrl))
      if (color) {
        return color.ref ? `${color.ref} - ${color.name}` : color.name
      }
      return ''
    },

    getColorDescriptionData(imagePath) {
      const imageUrl = imagePath.split('/').pop()
      const color = this.colors.find(c => c.image && c.image.includes(imageUrl))
      return color?.description || ''
    },

    getColorData(imagePath) {
      const imageUrl = imagePath.split('/').pop()
      const color = this.colors.find(c => c.image && c.image.includes(imageUrl))
      return color || null
    },

    openImageLightboxWithColor(imagePath) {
      this.selectedColorData = this.getColorData(imagePath)
      this.openImageLightbox(imagePath)
    },
  },
})
</script>

<style>
/* Testimonials Section Styles */
.testimonials-section {
  overflow: hidden;
}

.testimonials-bg-decoration {
  position: absolute;
  top: 0;
  right: 0;
  width: 300px;
  height: 300px;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, transparent 70%);
  border-radius: 50%;
  transform: translate(50%, -50%);
}

.testimonials-divider {
  width: 60px;
  height: 4px;
  background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
  margin: 0 auto;
  border-radius: 2px;
}

.testimonials-carousel-container {
  max-width: 1000px;
  margin: 0 auto;
}

.testimonials-carousel {
  background: transparent !important;
}

.testimonial-card {
  background: white;
  border-radius: 20px;
  padding: 32px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  position: relative;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  height: 280px;
  display: flex;
  flex-direction: column;
}

.testimonial-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 48px rgba(0, 0, 0, 0.12);
}

.testimonial-card.mobile {
  height: auto;
  min-height: 220px;
  padding: 24px;
}

.testimonial-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 20px;
}

.quote-icon {
  opacity: 0.7;
}

.testimonial-text {
  font-size: 16px;
  line-height: 1.6;
  color: #374151;
  font-weight: 400;
  flex-grow: 1;
  margin-bottom: 24px;
  font-style: italic;
}

.testimonial-text.mobile {
  font-size: 15px;
  margin-bottom: 20px;
}

.testimonial-rating {
  display: flex;
  gap: 2px;
  opacity: 0.8;
}

.testimonial-author {
  display: flex;
  align-items: center;
  gap: 12px;
}

.testimonial-author.mobile {
  gap: 10px;
}

.testimonial-avatar {
  border: 2px solid rgba(255, 255, 255, 0.8);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.author-info {
  flex: 1;
}

.author-name {
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 2px;
}

.author-source {
  font-size: 13px;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

@media (max-width: 768px) {
  .testimonials-bg-decoration {
    width: 200px;
    height: 200px;
  }

  .author-name {
    font-size: 14px;
  }

  .author-source {
    font-size: 12px;
  }
}

.square-1 {
  margin-top: -65px;
  width: 100%;
  height: calc(100% + 100px);
  background-color: #292d41;
  color: white;
  box-shadow: 0px 45px 80px 0px rgba(0, 0, 0, 0.7);
}

.carousel {
  --vc-nav-background: rgba(255, 255, 255, 0.7);
  --vc-nav-border-radius: 100%;
}

.carousel__prev,
.carousel__next {
  display: none;
}

.carousel__viewport {
  perspective: 2000px;
}

.carousel__track {
  transform-style: preserve-3d;
}

.carousel__slide--sliding {
  transition:
    opacity var(--carousel-transition),
    transform var(--carousel-transition);
}

.carousel.is-dragging .carousel__slide {
  transition:
    opacity var(--carousel-transition),
    transform var(--carousel-transition);
}

.carousel__slide {
  opacity: 1 !important;
  transform: none !important;
  min-width: 0;
}

.carousel__pagination {
  display: flex;
  justify-content: flex-start;
  align-items: center;
  margin-top: 50px;
  margin-left: 0;
  position: inherit;
}

.q-carousel__navigation {
  z-index: 10;
}

/* Otimização de imagens de fundo */
.filter-card,
.filter-card-3,
.filter-card-4 {
  background-repeat: no-repeat;
  background-size: 140%;
  background-position: center;
  background-color: 'transparent';
  will-change: background-image;
  transition: background-image 0.3s ease;
}

.border-radius-left {
  border-top-left-radius: 80px;
  border-top-right-radius: 80px;
  border-bottom-left-radius: 80px;
}

.border-radius-right {
  border-top-right-radius: 80px;
  border-bottom-left-radius: 80px;
  border-bottom-right-radius: 80px;
}

@media (max-width: 600px) {
  .img-slide {
    height: 300px;
    padding: 5px;
  }

  .carousel__pagination {
    justify-content: center;
    margin-top: 20px;
  }
}

.carousel__slide--active>.img-slide>.caption-slide {
  opacity: 1;
}

.glass-effect {
  background: rgba(0, 0, 0, 0.32);
  border-radius: 16px;
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  border: 1px solid rgba(0, 0, 0, 0.63);
}

/* Color Palette Gallery Styles */
.gallery-dialog {
  max-height: 100vh;
  overflow: hidden;
}

.gallery-masonry-container {
  height: calc(100vh - 140px);
  overflow-y: auto;
}

.masonry-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 16px;
}

@media (max-width: 1200px) {
  .masonry-grid {
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
  }
}

@media (max-width: 768px) {
  .masonry-grid {
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  }
}

@media (max-width: 480px) {
  .masonry-grid {
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  }
}

.masonry-item {
  border-radius: 12px;
  overflow: visible;
  cursor: pointer;
  transition: all 0.3s ease;
  position: relative;
  aspect-ratio: 3 / 4;
  margin-bottom: 12px;
}

.masonry-item:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
}

.masonry-image {
  width: 100%;
  height: 100%;
  display: block;
  border-radius: 12px;
  overflow: hidden;
}

.gallery-overlay {
  background: rgba(0, 0, 0, 0.6);
  opacity: 0;
  transition: opacity 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
}

.masonry-item:hover .gallery-overlay {
  opacity: 1;
}

.lightbox-image {
  max-width: 95vw;
  max-height: 95vh;
  cursor: pointer;
}

.color-name-label {
  background: #1a1a1a;
  color: white;
  text-align: center;
  padding: 10px 12px;
  font-size: 11px;
  font-weight: 600;
  border-radius: 0 0 12px 12px;
  margin-top: 4px;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  line-height: 1.3;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
  word-break: break-word;
}

.color-name-label.has-description {
  border-radius: 0;
  margin-bottom: 0;
}

.color-description-label {
  background: #2a2a2a;
  color: #d0d0d0;
  text-align: center;
  padding: 8px 12px;
  font-size: 10px;
  font-weight: 400;
  border-radius: 0 0 12px 12px;
  margin-top: -4px;
  line-height: 1.4;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
  word-break: break-word;
  font-style: italic;
}

@media (max-width: 768px) {
  .color-name-label {
    font-size: 10px;
    padding: 6px 8px;
  }

  .color-description-label {
    font-size: 9px;
    padding: 6px 8px;
  }
}

/* Clickable Cards Styles */
.clickable-card {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.clickable-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 16px 40px rgba(0, 0, 0, 0.3);
}

.clickable-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(45deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
  opacity: 0;
  transition: opacity 0.3s ease;
  z-index: 1;
}

.clickable-card:hover::before {
  opacity: 1;
}

.clickable-card .q-card-section {
  position: relative;
  z-index: 2;
}

.hover-btn {
  opacity: 0.8;
  transform: scale(0.95);
  transition: all 0.3s ease;
  backdrop-filter: blur(10px);
  background: rgba(255, 255, 255, 0.1) !important;
  border: 2px solid rgba(255, 255, 255, 0.8);
}

.clickable-card:hover .hover-btn {
  opacity: 1;
  transform: scale(1);
  background: rgba(255, 255, 255, 0.2) !important;
  border-color: white;
  box-shadow: 0 4px 15px rgba(255, 255, 255, 0.3);
}

@media (max-width: 768px) {
  .clickable-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
  }

  .hover-btn {
    opacity: 0.9;
    transform: scale(1);
  }
}

/* Fabric Animation Styles */
.fabric-animation-container {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  z-index: 1;
  overflow: hidden;
}

/* Floating Threads */
.floating-thread {
  position: absolute;
  width: 200px;
  height: 3px;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
  opacity: 0.8;
  box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
}

.thread-1 {
  top: 15%;
  left: -10%;
  animation: float-horizontal 15s infinite linear;
  transform: rotate(-10deg);
}

.thread-2 {
  top: 60%;
  right: -10%;
  animation: float-horizontal-reverse 20s infinite linear;
  transform: rotate(15deg);
}

.thread-3 {
  top: 35%;
  left: -10%;
  animation: float-horizontal 18s infinite linear;
  transform: rotate(-5deg);
  animation-delay: -5s;
}

/* Floating Yarn Balls */
.floating-yarn {
  position: absolute;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: radial-gradient(circle at 30% 30%,
      rgba(255, 255, 255, 0.3),
      rgba(255, 255, 255, 0.15));
  border: 2px solid rgba(255, 255, 255, 0.25);
  opacity: 0.7;
  box-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
}

.yarn-1 {
  top: 25%;
  right: 10%;
  animation: float-vertical 12s infinite ease-in-out;
}

.yarn-2 {
  bottom: 20%;
  left: 15%;
  animation: float-vertical 16s infinite ease-in-out;
  animation-delay: -8s;
}

/* Floating Fabric Patterns */
.floating-pattern {
  position: absolute;
  width: 100px;
  height: 100px;
  opacity: 0.2;
  background-image: repeating-linear-gradient(45deg,
      transparent,
      transparent 8px,
      rgba(255, 255, 255, 0.5) 8px,
      rgba(255, 255, 255, 0.5) 12px);
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.15);
  box-shadow: 0 0 20px rgba(255, 255, 255, 0.1);
}

.pattern-1 {
  top: 10%;
  left: 20%;
  animation: float-diagonal 20s infinite linear;
  transform: rotate(45deg);
}

.pattern-2 {
  bottom: 15%;
  right: 25%;
  animation: float-diagonal-reverse 25s infinite linear;
  transform: rotate(-30deg);
}

.pattern-3 {
  top: 70%;
  left: 60%;
  animation: float-vertical 14s infinite ease-in-out;
  transform: rotate(60deg);
  animation-delay: -7s;
}

/* Animation Keyframes */
@keyframes float-horizontal {
  0% {
    transform: translateX(-220px) rotate(-10deg);
  }

  100% {
    transform: translateX(calc(100vw + 20px)) rotate(-10deg);
  }
}

@keyframes float-horizontal-reverse {
  0% {
    transform: translateX(220px) rotate(15deg);
  }

  100% {
    transform: translateX(calc(-100vw - 20px)) rotate(15deg);
  }
}

@keyframes float-vertical {

  0%,
  100% {
    transform: translateY(0px) rotate(0deg);
  }

  50% {
    transform: translateY(-30px) rotate(180deg);
  }
}

@keyframes float-diagonal {
  0% {
    transform: translate(-100px, -100px) rotate(45deg);
    opacity: 0;
  }

  25% {
    opacity: 0.2;
  }

  75% {
    opacity: 0.2;
  }

  100% {
    transform: translate(calc(100vw + 100px), calc(100vh + 100px)) rotate(405deg);
    opacity: 0;
  }
}

@keyframes float-diagonal-reverse {
  0% {
    transform: translate(100px, 100px) rotate(-30deg);
    opacity: 0;
  }

  25% {
    opacity: 0.2;
  }

  75% {
    opacity: 0.2;
  }

  100% {
    transform: translate(calc(-100vw - 100px), calc(-100vh - 100px)) rotate(-390deg);
    opacity: 0;
  }
}

/* Reduce animations on mobile for performance */
@media (max-width: 768px) {

  .floating-thread,
  .floating-yarn,
  .floating-pattern {
    display: none;
  }
}

/* Reduce motion for users who prefer it */
@media (prefers-reduced-motion: reduce) {

  .floating-thread,
  .floating-yarn,
  .floating-pattern {
    animation: none;
  }
}

/* Hero Mobile Large Text Styles */
@media (max-width: 768px) {
  .hero-mobile-text {
    height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .hero-mobile-title {
    font-size: 4rem !important;
    line-height: 1.2 !important;
    letter-spacing: -2px;
    display: block;
    margin-bottom: 0.5rem;
  }
}

@media (max-width: 480px) {
  .hero-mobile-title {
    font-size: 3.5rem !important;
    line-height: 1.15 !important;
    margin-bottom: 0.4rem;
  }
}

@media (max-width: 360px) {
  .hero-mobile-title {
    font-size: 3rem !important;
    line-height: 1.1 !important;
    margin-bottom: 0.3rem;
  }
}

/* Palette Card Custom Hover Animation */
.palette-card-hover {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  position: relative;
  overflow: hidden;
}

.palette-card-hover:hover {
  transform: translateY(-8px);
  box-shadow: 0 16px 40px rgba(0, 0, 0, 0.3);
}

.palette-card-hover::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(45deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
  opacity: 0;
  transition: opacity 0.3s ease;
  z-index: 1;
  pointer-events: none;
}

.palette-card-hover:hover::before {
  opacity: 1;
}

.palette-card-hover .q-card-section {
  position: relative;
  z-index: 2;
}

.palette-btn {
  position: relative;
  z-index: 3;
  pointer-events: auto;
  cursor: pointer;
}

.palette-card-hover:hover .palette-btn {
  opacity: 1;
  transform: scale(1.05);
  background: rgba(255, 255, 255, 0.2) !important;
  border-color: white;
  box-shadow: 0 4px 15px rgba(255, 255, 255, 0.3);
}

@media (max-width: 768px) {
  .palette-card-hover:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
  }
}
</style>
