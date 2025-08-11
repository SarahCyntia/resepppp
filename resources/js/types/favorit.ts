import { Resep } from "./resep";

export interface Favorit {
  id: number;
  user_id: number;
  resep_id: number;
  resep?: Resep;
  created_at?: string;
}
