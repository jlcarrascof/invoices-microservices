const express = require('express');
const app = express();
const PORT = 3001;

// Middleware to parse JSON
app.use(express.json());

// Health check route
app.get('/', (req, res) => {
  res.json({ message: 'Hello Node! Notifications Service Ready' });
});

// Route to receive notifications (simulated for now)
app.post('/notify', (req, res) => {
  const { type, recipient, message } = req.body;
  console.log(`[NOTIFICATION] Type: ${type}, Recipient: ${recipient}, Message: ${message}`);
  res.json({ 
    success: true, 
    notification: { type, recipient, message, timestamp: new Date() } 
  });
});

// Start server
app.listen(PORT, () => {
  console.log(`✅ Notifications Service running on http://localhost:${PORT}`);
});
