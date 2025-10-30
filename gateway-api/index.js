const express = require('express');
const app = express();
const PORT = 4000;

// Middleware to parse JSON
app.use(express.json());

// Health check route
app.get('/', (req, res) => {
  res.json({ message: 'Gateway API Ready', version: '1.0.0' });
});

// Status route
app.get('/health', (req, res) => {
  res.json({ status: 'OK', timestamp: new Date() });
});

// Start server
app.listen(PORT, () => {
  console.log(`✅ Gateway API running on http://localhost:${PORT}`);
});
