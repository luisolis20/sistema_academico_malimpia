/**
 * Configuración de Webpack para producción y desarrollo
 * Aquí se definen las configuraciones específicas de Webpack
 * que se usarán en el entorno de desarrollo y producción
 * Para mandar a producción, ejecutar `npm run build`, allí se creará un directorio
 * `dist` con los archivos estáticos y el código JavaScript optimizado para solo ser subido al
 * servidor
 */
const { defineConfig } = require("@vue/cli-service");// DefineConfig es una función de vue-cli-service
const webpack = require("webpack");// Importa Webpack

/**
 * Configuración de Webpack para desarrollo y producción
 */
module.exports = defineConfig({
  transpileDependencies: true,//Habilita transpilación de dependencias
  publicPath: process.env.NODE_ENV === "production" ? "/sistma/" : "/",//Define la ruta de acceso público

  //  Carpeta limpia para build
  outputDir: "dist",//Directorio de salida
  assetsDir: "assets",//Directorio de recursos

  productionSourceMap: false, // Evita que el navegador muestre código fuente (map files)
  /**
   * Configuración de Webpack para desarrollo y producción
   */
  configureWebpack: {
    //Optimization para Webpack
    optimization: {
      minimize: true, //  Minifica todo el código JS y CSS
      splitChunks: {
        chunks: "all",
      },
    },
    //Plugins para Webpack
    plugins: [
      
      //  Define variables globales seguras para tus URLs
      new webpack.DefinePlugin({
        __API_SITMA__: JSON.stringify(
          process.env.NODE_ENV === "production"
            ? "http://backmilenio.test/api"
            : "http://backmilenio.test/api"
        ),//Define la URL de tu API, en producción se usará el valor de la variable de entorno y en desarrollo se usará el valor predeterminado (cuando mandes a producción debes cambiar la url por la de tu web)

      }),
    ],
    //Configuración de archivos de salida hasheados aleatorios
    output: {
      //  Archivos con hash aleatorio (ocultan nombres)
      filename: "assets/js/[name].[contenthash].js",//Archivos con hash aleatorio (ocultan nombres)
      chunkFilename: "assets/js/[name].[contenthash].js",//Archivos con hash aleatorio (ocultan nombres)  
    },
  },
  /**
   * Configuración de la devServer
   * Aqui se configuran las opciones de la devServer
   * Proxy permite redireccionar las peticiones a tu API a tu entorno de desarrollo
   * El proxy se configura en el entorno de desarrollo, contiene la configuración de la API
   * 
  */    
  devServer: {
    proxy: {
      "/api": {
        target: "http://backmilenio.test",
        changeOrigin: true,
        pathRewrite: { "^/api": "" },
      },
    },
  },
});
