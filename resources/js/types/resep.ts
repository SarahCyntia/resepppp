import { Tag } from "element-plus";
import { Kategori } from "./kategori";
import { User } from "./user";
import { Bahan } from "./bahan";
import { Langkah } from "./langkah";
import { Komentar } from "./komentar";
import { Rating } from "./rating";

export interface Resep {
  id: number;
  user_id: number;
  user?: User;
  judul: string;
  deskripsi: string;
  gambar?: string;
  waktu_masak: string;
  kategori?: Kategori[];
  tag?: Tag[];
  bahan?: Bahan[];
  langkah?: Langkah[];
  komentar?: Komentar[];
  rating?: Rating[];
  rata_rata_rating?: number;
  is_favorit?: boolean;
  created_at?: string;
  updated_at?: string;
}
