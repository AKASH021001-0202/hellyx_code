import express from "express";
import { authApi } from "./auth.js"; // auth middleware
import { CartModel } from "../../db.utils/model.js";

const AddtoCartrouter = express.Router();

/**
 * @route POST /cart
 * @desc Add product to user's cart
 * @access Private
 */

// AddtoCartrouter.js or cart.js
AddtoCartrouter.get("/", authApi, async (req, res) => {
  try {
    const userId = req.user._id;

    const cart = await CartModel.findOne({ userId });

    if (!cart || cart.items.length === 0) {
      return res.json({ items: [], total: 0 });
    }

    const total = cart.items.reduce((sum, item) => sum + item.price * item.quantity, 0);

    res.json({ items: cart.items, total });
  } catch (err) {
    console.error("Fetch Cart Error:", err);
    res.status(500).json({ message: "Server error" });
  }
});


AddtoCartrouter.delete("/:productId", authApi, async (req, res) => {
  const userId = req.user._id;
  const productId = req.params.productId;

  try {
    const cart = await CartModel.findOne({ userId });

    if (!cart) {
      return res.status(404).json({ message: "Cart not found" });
    }

    // Filter out the item with matching productId
    cart.items = cart.items.filter(
      item => item.productId.toString() !== productId
    );

    cart.updatedAt = new Date();
    await cart.save();

    res.status(200).json({ message: "Item removed from cart", cart });
  } catch (err) {
    console.error("Cart Delete Error:", err);
    res.status(500).json({ message: "Server error" });
  }
});

AddtoCartrouter.post("/", authApi, async (req, res) => {
  try {
    const userId = req.user._id; // Now properly set by authApi
    const { _id, name, price, image } = req.body;

    if (!_id || !name || price === undefined || !image) {
      return res.status(400).json({ message: "Missing product details" });
    }

    let cart = await CartModel.findOne({ userId });

    if (!cart) {
      cart = new CartModel({
        userId,
        items: [{ productId: _id, name, price, image, quantity: 1 }]
      });
    } else {
      const existingItem = cart.items.find(
        item => item.productId.toString() === _id.toString()
      );

      if (existingItem) {
        existingItem.quantity += 1;
      } else {
        cart.items.push({ productId: _id, name, price, image, quantity: 1 });
      }
    }

    await cart.save();
    res.status(200).json({ message: "Item added to cart", cart });
    
  } catch (err) {
    console.error("Cart Error:", err);
    res.status(500).json({ message: "Server error", error: err.message });
  }
});

AddtoCartrouter.get("/cart", (req, res) => {
  res.status(200).json({ message: "Cart API works. Use POST/GET/DELETE methods via frontend." });
});


export default AddtoCartrouter;
