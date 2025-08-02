import express from "express";
import { authApi } from "./auth.js"; // middleware for token check
import { Usermodel } from "../../db.utils/model.js";

const CustomerRouter = express.Router();

/**
 * @route GET /admin/customers
 * @desc Get all customers (role: "user")
 * @access Private (admin only)
 */
CustomerRouter.get("/", async (req, res) => {
  try {
    if (req.user.role !== "admin") {
      return res.status(403).json({ message: "Access denied" });
    }

    const customers = await Usermodel.find({ role: "user" }).select("name email phone createdAt");
    res.status(200).json({ customers });
  } catch (err) {
    console.error("Customer fetch error:", err);
    res.status(500).json({ message: "Server error" });
  }
});

export default CustomerRouter;
