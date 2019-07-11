const proxy = [
  {
    context: '/api',
    target: 'http://localhost:9999',
    pathRewrite: {'^/api' : ''}
  }
];
module.exports = proxy;
