import express, { Request, Response, NextFunction } from 'express';
import cors from 'cors';
import morgan from 'morgan';
import dotenv from 'dotenv';
import { z } from 'zod';

dotenv.config();

const app = express();
const port = process.env.PORT || 3200;

app.use(cors());
app.use(express.json());
app.use(morgan('dev'));

// Sample route
app.get('/', (req, res) => {
  res.send('Hello, TypeScript + Express!');
});

// 404 Not Found Middleware
app.use((req: Request, res: Response) => {
  res.status(404).json({
    status: 'error',
    message: 'Route not found',
  });
});

// 500 Error Handling Middleware
app.use((err: Error, req: Request, res: Response, next: NextFunction) => {
  console.error('Internal Server Error:', err.stack);

  res.status(500).json({
    status: 'error',
    message: 'Internal Server Error',
  });
});

app.listen(port, () => {
  console.log(`Server is running on http://localhost:${port}`);
});
