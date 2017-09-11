var http = require('http');

http.createServer(function (request, response) {
    response.writeHead(200, {'Content-Type': 'text/plain'});
    response.end('NodeJS Web Project (' + process.env.DOCKER_CONTAINER_NAME + ')\n');
}).listen(9000);

console.log('Server running at http://127.0.0.1:9000/...');
