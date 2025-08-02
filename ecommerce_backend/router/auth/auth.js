// auth.js
import jwt from "jsonwebtoken";
import bcrypt from "bcryptjs";
import { Usermodel } from "../../db.utils/model.js";

const jwtSecret = process.env.JWT_SECRET || "your_default_jwt_secret_key";

// Generate JWT token
const generateToken = (user) => {
  return jwt.sign(
    {
      id: user._id,
      name: user.name,
      email: user.email,
      role: user.role
    },
    jwtSecret,
    { expiresIn: "1h" }
  );
};
// Authenticate user and return token + user info
const authenticateUser = async (email, password) => {
  const user = await Usermodel.findOne({ email });

  if (!user) {
    throw new Error("User not found");
  }

  const isMatch = await bcrypt.compare(password, user.password);
  if (!isMatch) {
    throw new Error("Invalid credentials");
  }

    const token = generateToken(user);
  
  return {
    token,
    user: {
      id: user._id,
      name: user.name,
      email: user.email,
      role: user.role || "customer",
    },
  };
};const authApi = async (req, res, next) => {
  const token = req.header('Authorization')?.replace('Bearer ', '');

  if (!token) {
    return res.status(401).json({ message: 'Access denied. No token provided.' });
  }

  try {
    const decoded = jwt.verify(token, process.env.JWT_SECRET);
    
    const user = await Usermodel.findById(decoded.userId); // ✅ correct

    if (!user) {
      return res.status(401).json({ message: 'Invalid token.' });
    }

    req.user = user;
    next();
  } catch (error) {
    res.status(401).json({ message: 'Invalid token.' });
  }
};


// Middleware to check if user is admin
const isAdmin = (req, res, next) => {
  if (req.user?.role !== 'admin') {
    return res.status(403).json({ message: "Access denied. Admins only." });
  }
  next();
};

export { authenticateUser, authApi, isAdmin };
