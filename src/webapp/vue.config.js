module.exports = {
  pluginOptions: {
    apollo: {
      enableMocks: false,
      enableEngine: false
    }
  },
  configureWebpack: {
    module: {
      rules: [
        { test: /\.md$/, use: 'raw-loader' }
      ]
    }
  },
  pwa: {
    name: 'Oasys',
    themeColor: '#7f267b',
    msTileColor: '#b837b3',
    appleMobileWebAppCapable: 'yes',
    appleMobileWebAppStatusBarStyle: '#b837b3',
    iconPaths: {
      favicon32: 'img/icons/favicon-32x32.png',
      favicon16: 'img/icons/favicon-16x16.png',
      appleTouchIcon: 'img/icons/apple-icon-152x152.png',
      msTileImage: 'img/icons/msapplication-icon-144x144.png'
    }
  }
}
