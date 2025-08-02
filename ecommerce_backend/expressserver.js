import express from "express";
import cors from "cors";
import dotenv from "dotenv";
import mongooseDb from "./db.utils/mongoose-connection.js";
import RegisterRouter from "./router/auth/register.js";
import LoginRouter from "./router/auth/login.js";
import ForgetPassword from "./router/controller/forgetpassword.js";
import ResetPassword from "./router/controller/resetpassword.js";
import UserRouter from "./router/user.js";
import ActivateRouter from "./router/auth/activater.js";
import jwt from 'jsonwebtoken';
import { Usermodel } from "./db.utils/model.js";
import path from "path";
import bodyParser from "body-parser";
import ProductRouter from "./router/auth/product.js";
import AddtoCartrouter from "./router/auth/cart.js";
import orderRouter from "./router/auth/order.js";
import CustomerRouter from "./router/auth/customer.js";

// Load environment variables from .env file
dotenv.config();

const server = express();

// Connect to MongoDB using mongoose
await mongooseDb();

server.use(bodyParser.urlencoded({ extended: true }));
server.use(bodyParser.json()); 

// Middleware
server.use(express.json());
server.use(cors({
  origin: '*',
methods: ["GET", "POST", "PUT", "DELETE"],
  allowedHeaders: ["Content-Type", "Authorization"],
   credentials: true,
}));

// Custom middleware to log requests
const customMiddleware = (req, res, next) => {
  console.log(
    new Date().toISOString(),
    "Handling Request",
    req.method,
    req.originalUrl
  );
  next();
};

// Auth Middleware for routes that need protection
const authMiddleware = async (req, res, next) => {
  const token = req.header('Authorization')?.replace('Bearer ', '');
  if (!token) {
    return res.status(401).json({ error: 'No token, authorization denied' });
  }

  try {
    const decoded = jwt.verify(token, process.env.JWT_SECRET);
    const user = await Usermodel.findById(decoded.userId);
    if (!user) {
      return res.status(401).json({ error: 'User not found, authorization denied' });
    }

    req.user = user;
    next();
  } catch (error) {
    res.status(401).json({ error: 'Token is not valid' });
  }
};

// Apply middleware globally
server.use(customMiddleware);


// Routes that don't require authentication
server.use("/register", RegisterRouter);
server.use("/login", LoginRouter);
server.use("/user", UserRouter);
server.use("/activate", ActivateRouter);
server.use("/forget-password", ForgetPassword);
server.use("/reset-password", ResetPassword);
server.use("/products", ProductRouter);
server.use("/cart", AddtoCartrouter);
server.use("/order", orderRouter);
server.use("/customers", CustomerRouter);

// Start server
const port = process.env.PORT || 8000;
server.listen(port, () => {
  console.log("Server is running on port", port);
});
