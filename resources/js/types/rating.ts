import { User } from "./user";

export interface Rating {
  id: number;
  user_id: number;
  resep_id: number;
  nilai: number; // biasanya 1–5
  user?: User;
  created_at?: string;
  updated_at?: string;
}
