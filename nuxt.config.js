export default {
    buildModules: [
        '@nuxtjs/color-mode',
        '@nuxtjs/tailwindcss',
    ],
    colorMode: {
        classSuffix: ''
    },
    head: {
        title: 'MuseumBots',
        meta: [
          { charset: 'utf-8' },
          { name: 'viewport', content: 'width=device-width, initial-scale=1' },
          {
            hid: 'description',
            name: 'description',
            content: 'Bunch of projects of small Twitter bots about culture, museums and heritage.'
          }
        ],
        link: [{ rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }],
        bodyAttrs: {
            class: 'bg-white dark:bg-black'
        }
    },
    ssr: false,
    target: 'static'
}
