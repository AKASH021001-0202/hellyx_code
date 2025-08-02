import mongoose from "mongoose";
import bcrypt from "bcryptjs";

const userSchema = new mongoose.Schema(
  {
    name: {
      type: String,
      required: true,
      trim: true
    },
    email: {
      type: String,
      unique: true,
      required: true,
      lowercase: true,
      trim: true,
      match: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/
    },
    phone: {
      type: String,
      required: false,
      match: /^[0-9]{10}$/
    },
    password: {
      type: String,
      required: true
    },
    role: {
      type: String,
      enum: ['user', 'admin'],
      default: 'user'
    },
    isActive: {
      type: Boolean,
      default: false
    },
    activationToken: {
      type: String,
      required: false,
      index: true
    },
    activationTokenExpires: {
      type: Date,
      required: false
    },
    resetPasswordToken: {
      type: String,
      required: false,
      index: true
    },
    resetPasswordExpires: {
      type: Date,
      required: false
    }
  },
  { timestamps: true }
);



const Usermodel = mongoose.model("User", userSchema, "Users");


const productSchema = new mongoose.Schema(
  {
    name: {
      type: String,
      required: true,
      trim: true,
    },
    description: {
      type: String,
      default: "",
    },
    price: {
      type: Number,
      required: true,
      min: [0.01, "Price must be greater than 0"],
    },
    stock_quantity: {
      type: Number,
      required: true,
      min: [0, "Stock cannot be negative"],
    },
    image: {
      type: String, // URL or relative path
      default: "",
    },
    created_by: {
      type: mongoose.Schema.Types.ObjectId,
      ref: "User", // optional: to track admin who created it
    },
  },
  {
    timestamps: true, // adds createdAt and updatedAt fields
  }
);
const ProductModel = mongoose.model("Product", productSchema,"Products");

const CartSchema = new mongoose.Schema({
  userId: {
    type: mongoose.Schema.Types.ObjectId,
    ref: "User",
    required: true,
  },
  items: [
    {
      productId: {
        type: mongoose.Schema.Types.ObjectId,
        ref: "Product",
      },
      name: String,
      price: Number,
      image: String,
      quantity: {
        type: Number,
        default: 1,
      },
    },
  ],
  updatedAt: {
    type: Date,
    default: Date.now,
  },
});

const CartModel = mongoose.model("Cart", CartSchema ,"AddtoCart");

const OrderSchema = new mongoose.Schema({
  userId: {
    type: mongoose.Schema.Types.ObjectId,
    ref: "User",
    required: true,
  },
  items: [
    {
      productId: {
        type: mongoose.Schema.Types.ObjectId,
        ref: "Product",
      },
      name: String,
      price: Number,
      image: String,
      quantity: Number,
    },
  ],
  status: {
    type: String,
    enum: ["pending", "accepted", "shipped", "delivered", "cancelled"],
    default: "pending",
  },
  createdAt: {
    type: Date,
    default: Date.now,
  },
});

 const OrderModel = mongoose.model("Order", OrderSchema ,"orders");

// Mongoose Model
const ImageTourlSchema = new mongoose.Schema({
  filename: String,
  imageUrl: String,
});
const ImageToUrlModel = mongoose.model("ImageToUrl", ImageTourlSchema ,"imagetourls");

export { Usermodel ,ImageToUrlModel, ProductModel,CartModel,OrderModel};
  