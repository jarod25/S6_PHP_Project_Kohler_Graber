const Encore = require('@symfony/webpack-encore');
const path = require('path');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore.setOutputPath('public/build')
    .setPublicPath('/build')
    .addEntry('main', './assets/main/js/base.js')

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk().configureBabel((config) => {
    config.plugins.push('@babel/plugin-proposal-class-properties');
})

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    }).cleanupOutputBeforeBuild().enableSourceMaps(!Encore.isProduction()).enableVersioning(Encore.isProduction()).enableSassLoader(function (options) {
    // options.includePaths = [...]
    options.sassOptions.sourceComments = false;
    options.sassOptions.outputStyle = 'compressed';
}, {
    resolveUrlLoader: false,
}).addAliases({
    '#': path.resolve(__dirname, 'assets/main'),
    'font': path.resolve(__dirname, 'assets/main/font'),
    '#images': path.resolve(__dirname, 'assets/main/img'),
    '#node_modules': path.resolve(__dirname, 'node_modules'),
});

if (Encore.isProduction()) {
    Encore.copyFiles({
        from: './assets/main/img',
        to: 'images/[path][name].[hash:8].[ext]',
        pattern: /\.(png|jpg|jpeg|gif|ico|svg|webp)$/,
    });
} else {
    Encore.copyFiles({
        from: './assets/main/img',
        to: 'images/[path][name].[ext]',
        pattern: /\.(png|jpg|jpeg|gif|ico|svg|webp)$/,
    });
}

Encore.copyFiles({
    from: './assets/main/font',
    to: 'fonts/[path][name].[ext]',
    pattern: /\.(woff|woff2)$/,
});

// Retrieve the config
const defaultConfig = Encore.getWebpackConfig();
defaultConfig.name = 'mainConfig';
defaultConfig.experiments = {
    // required for import stripe-js
    topLevelAwait: true,
};

module.exports = defaultConfig;
