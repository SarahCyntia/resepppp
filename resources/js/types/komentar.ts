import { User } from "./user";

export interface Komentar {
  id: number;
  user_id: number;
  resep_id: number;
  isi: string;
  user?: User;
  created_at?: string;
  updated_at?: string;
}
