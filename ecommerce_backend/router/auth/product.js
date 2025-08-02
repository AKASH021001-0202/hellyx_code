import express from "express";
import dotenv from "dotenv";
import { ProductModel } from "../../db.utils/model.js";

dotenv.config();
const ProductRouter = express.Router();

/**
 * @route POST /products
 * @desc Create a product
 */
ProductRouter.post("/", async (req, res) => {
  const { name, description, price, stock_quantity, image } = req.body;

  if (!name || !description || !image || price <= 0 || stock_quantity < 0) {
    return res.status(400).json({ message: "Invalid input. All fields are required." });
  }

  try {
    const product = new ProductModel({
      name,
      description,
      price,
      stock_quantity,
      image,
      createdAt: new Date(),
      updatedAt: new Date(),
    });

    await product.save();
    return res.status(201).json({ message: "Product created", product });
  } catch (err) {
    console.error("Product creation error:", err);
    return res.status(500).json({ message: "Server error" });
  }
});

/**
 * @route GET /products
 * @desc Get all products with pagination, sort, and search
 */
ProductRouter.get("/", async (req, res) => {
  const {
    page = 1,
    limit = 20,
    sortBy = "createdAt",
    order = "desc",
    search = "",
  } = req.query;

  try {
    const filter = search
      ? { name: { $regex: search, $options: "i" } }
      : {};

    const products = await ProductModel.find(filter)
      .sort({ [sortBy]: order === "desc" ? -1 : 1 })
      .skip((page - 1) * limit)
      .limit(Number(limit));

    const count = await ProductModel.countDocuments(filter);

    return res.json({
      total: count,
      page: Number(page),
      pages: Math.ceil(count / limit),
      products,
    });
  } catch (err) {
    console.error("Product fetch error:", err);
    res.status(500).json({ message: "Server error" });
  }
});

/**
 * @route GET /products/:id
 * @desc Get a single product by ID
 */
ProductRouter.get("/:id", async (req, res) => {
  try {
    const product = await ProductModel.findById(req.params.id);
    if (!product) return res.status(404).json({ message: "Product not found" });

    res.json(product);
  } catch (err) {
    console.error("Get product error:", err);
    res.status(500).json({ message: "Server error" });
  }
});

/**
 * @route PUT /products/:id
 * @desc Update a product
 */
ProductRouter.put("/:id", async (req, res) => {
  const updates = { ...req.body, updatedAt: new Date() };

  try {
    const product = await ProductModel.findByIdAndUpdate(req.params.id, updates, {
      new: true,
      runValidators: true,
    });

    if (!product) return res.status(404).json({ message: "Product not found" });

    res.json({ message: "Product updated", product });
  } catch (err) {
    console.error("Product update error:", err);
    res.status(500).json({ message: "Server error" });
  }
});

/**
 * @route DELETE /products/:id
 * @desc Delete a product
 */
ProductRouter.delete("/:id", async (req, res) => {
  try {
    const product = await ProductModel.findByIdAndDelete(req.params.id);
    if (!product) return res.status(404).json({ message: "Product not found" });

    res.json({ message: "Product deleted" });
  } catch (err) {
    console.error("Product delete error:", err);
    res.status(500).json({ message: "Server error" });
  }
});

export default ProductRouter;
