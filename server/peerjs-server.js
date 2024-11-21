const { PeerServer } = require('peer');

const peerServer = PeerServer({
    port: 9000,
    path: '/peerjs',
    ssl: {
        key: '/path/to/your/ssl/key.pem',    // If using HTTPS
        cert: '/path/to/your/ssl/cert.pem'   // If using HTTPS
    }
});

peerServer.on('connection', (client) => {
    console.log(`Client connected: ${client.getId()}`);
});

peerServer.on('disconnect', (client) => {
    console.log(`Client disconnected: ${client.getId()}`);
}); 