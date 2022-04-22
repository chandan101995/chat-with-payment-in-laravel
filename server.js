const express = require('express');
const app = express();
const server = require('http').createServer(app);
var users = [];

const io = require('socket.io')(server, {
    cors: { origin: "*"}
});

io.on('connection', (socket) => {
    console.log('connection');

    socket.on("user_connected", function (user_id)
    {
        users[user_id] = socket.id;
        io.emit('updateUserStatus', users);
        console.log("user connected "+ user_id);
    });

    socket.on('sendChatToServer', (message) =>
    {
        console.log(message);
        io.sockets.emit('sendChatToClient', message);
        // socket.broadcast.emit('sendChatToClient', message);
    });

    socket.on('typing', (data)=>{
        if(data.typing==true)
           io.emit('display', data)
        else
           io.emit('display', data)
    })
    
    socket.on('disconnect', (socket) => {
        var i = users.indexOf(socket.id);
        users.splice(i, 1, 0);
        io.emit('updateUserStatus', users);
        console.log(users);
    });
});

server.listen(3000, () => {
    console.log('Server is running');
});