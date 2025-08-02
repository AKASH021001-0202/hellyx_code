import express from "express";
import bcrypt from "bcryptjs";
import jwt from "jsonwebtoken";
import dotenv from "dotenv";
import { Usermodel } from "../../db.utils/model.js";

dotenv.config();
const jwtSecret = process.env.JWT_SECRET;
const LoginRouter = express.Router();

LoginRouter.post("/", async (req, res) => {
  const { email, password, role } = req.body;
  
  // Input validation
  if (!email || !password || !role) {
    return res.status(400).json({ message: 'Email, password and role are required' });
  }

  try {
    // Find user with case-insensitive email
    const user = await Usermodel.findOne({ 
      email: { $regex: new RegExp(`^${email}$`, 'i') }
    }).select("+password");

    if (!user) {
      return res.status(401).json({ message: 'Invalid credentials' });
    }

    // Role check
    if (user.role !== role) {
      return res.status(403).json({ 
        message: `Access denied. This account is for ${user.role}s only` 
      });
    }

    // Password comparison
    const isMatch = await bcrypt.compare(password.trim(), user.password);
    if (!isMatch) {
      return res.status(401).json({ message: "Invalid credentials" });
    }

    // Generate JWT token with expiration
// In your login route
const token = jwt.sign(
  {
    id: user._id,  // Must match auth middleware expectation
    role: user.role
  },
  jwtSecret,
  { expiresIn: '1h' }  // Always set expiration
);
    // Secure response - remove password
    const userResponse = {
      id: user._id,
      name: user.name,
      email: user.email,
      role: user.role
    };

    res.status(200).json({ 
      token, 
      user: userResponse,
      expiresIn: 3600 // Token expires in 1 hour (in seconds)
    });

  } catch (err) {
    console.error("Login error:", err);
    res.status(500).json({ 
      message: "Authentication failed",
      error: process.env.NODE_ENV === 'development' ? err.message : null
    });
  }
});

export default LoginRouter;