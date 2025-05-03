const express = require('express');
const router = express.Router();
const { calculateTripDetails } = require('../controllers/tripController');

router.post('/calculate', calculateTripDetails);

module.exports = router; 