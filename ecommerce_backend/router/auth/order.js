import express from "express";
import { authApi } from "./auth.js"; // middleware to verify JWT
import { CartModel, OrderModel } from "../../db.utils/model.js";

const orderRouter = express.Router();

/**
 * @route POST /order
 * @desc Place order from cart
 * @access Private
 */
orderRouter.post("/", authApi, async (req, res) => {
  const userId = req.user._id;

  try {
    const cart = await CartModel.findOne({ userId });
    if (!cart || cart.items.length === 0) {
      return res.status(400).json({ message: "Cart is empty" });
    }

    const order = new OrderModel({
      userId,
      items: cart.items,
      status: "pending",
    });

    await order.save();
    await CartModel.deleteOne({ userId });

    res.status(201).json({ message: "Order placed. Awaiting admin approval.", order });
  } catch (err) {
    console.error("Order Creation Error:", err);
    res.status(500).json({ message: "Server error" });
  }
});

/**
 * @route GET /order/my-orders
 * @desc Get logged-in user's orders
 * @access Private
 */
orderRouter.get("/my-orders", authApi, async (req, res) => {
  try {
    const userId = req.user._id;
    const orders = await OrderModel.find({ userId }).sort({ createdAt: -1 });
    res.status(200).json({ orders });
  } catch (err) {
    console.error("My Orders Fetch Error:", err);
    res.status(500).json({ message: "Server error" });
  }
});

/**
 * @route GET /order/all
 * @desc Admin: Get all orders
 * @access Admin only (you can enhance this with role check)
 */
orderRouter.get("/admin-orders", async (req, res) => {
  try {
    const orders = await OrderModel.find()
    
      .sort({ createdAt: -1 });

    res.status(200).json({ orders });
  } catch (err) {
    console.error("All Orders Fetch Error:", err);
    res.status(500).json({ message: "Server error" });
  }
});

/**
 * @route PUT /order/:id/accept
 * @desc Admin accepts the order
 * @access Admin only
 */
orderRouter.put("/:id/accept", async (req, res) => {
  try {
    const order = await OrderModel.findById(req.params.id);
    if (!order) {
      return res.status(404).json({ message: "Order not found" });
    }

    order.status = "accepted";
    await order.save();

    res.status(200).json({ message: "Order accepted", order });
  } catch (err) {
    console.error("Accept Order Error:", err);
    res.status(500).json({ message: "Server error" });
  }
});

export default orderRouter;
