/*
 Navicat Premium Dump SQL

 Source Server         : PPLL
 Source Server Type    : MySQL
 Source Server Version : 80030 (8.0.30)
 Source Host           : localhost:3306
 Source Schema         : wisata_ppll

 Target Server Type    : MySQL
 Target Server Version : 80030 (8.0.30)
 File Encoding         : 65001

 Date: 30/11/2025 20:34:45
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin`  (
  `id_admin` int NOT NULL AUTO_INCREMENT,
  `nama_admin` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `level` enum('superadmin','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'admin',
  PRIMARY KEY (`id_admin`) USING BTREE,
  UNIQUE INDEX `username`(`username` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin
-- ----------------------------
INSERT INTO `admin` VALUES (1, 'superadmin', 'superadmin', 'superadmin', 'superadmin@gmail.com', 'superadmin');
INSERT INTO `admin` VALUES (2, 'admin', 'admin', 'admin', 'admin@gmail.com', 'admin');

-- ----------------------------
-- Table structure for kategori_wisata
-- ----------------------------
DROP TABLE IF EXISTS `kategori_wisata`;
CREATE TABLE `kategori_wisata`  (
  `id_kategori` int NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_kategori`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of kategori_wisata
-- ----------------------------
INSERT INTO `kategori_wisata` VALUES (1, 'Bukit');
INSERT INTO `kategori_wisata` VALUES (2, 'Pantai');

-- ----------------------------
-- Table structure for wisata
-- ----------------------------
DROP TABLE IF EXISTS `wisata`;
CREATE TABLE `wisata`  (
  `id_wisata` int NOT NULL AUTO_INCREMENT,
  `id_kategori` int NULL DEFAULT NULL,
  `nama_wisata` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `lokasi_wisata` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `jam_operasi` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `harga` decimal(10, 2) NULL DEFAULT NULL,
  `latitude` decimal(10, 7) NULL DEFAULT NULL,
  `longitude` decimal(10, 7) NULL DEFAULT NULL,
  PRIMARY KEY (`id_wisata`) USING BTREE,
  INDEX `id_kategori`(`id_kategori` ASC) USING BTREE,
  CONSTRAINT `wisata_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_wisata` (`id_kategori`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of wisata
-- ----------------------------
INSERT INTO `wisata` VALUES (1, 1, 'Bukit Arosbaya', 'Formasi batu kapur dan pemandangan alam yang indah, populer untuk hiking ringan.', 'Arosbaya, Bangkalan', '', 5000.00, -6.9474130, 112.8595340);
INSERT INTO `wisata` VALUES (2, 1, 'Bukit Jaddih', 'Bukit kapur dengan pemandangan tebing kapur yang unik dan spot foto menarik.', 'Socah, Bangkalan', '07.00 - 16.00 WIB', 10000.00, -7.0822830, 112.7595400);
INSERT INTO `wisata` VALUES (3, 1, 'Bukit Geger', 'Bukit dengan area perbukitan dan pemandangan laut dari ketinggian.', 'Geger, Bangkalan', '', 5000.00, -7.0288890, 112.9330560);
INSERT INTO `wisata` VALUES (4, 2, 'Pantai Rongkang', 'Pantai dengan batu-batu besar dan pemandangan ombak yang dramatis.', 'Kwanyar, Bangkalan', 'Buka 24 Jam', 5000.00, -7.1644380, 112.8419530);
INSERT INTO `wisata` VALUES (5, 2, 'Pantai Siring Kemuning', 'Pantai dengan pasir putih dan lokasi yang cocok untuk piknik keluarga.', 'Tanjung Bumi, Bangkalan', 'Pagi sampai Sore', 3000.00, -6.8849340, 113.0527800);
INSERT INTO `wisata` VALUES (6, 2, 'Pantai Rindu', 'Pantai populer dengan pemandangan laut yang luas dan area rekreasi.', 'Labang, Bangkalan', 'Buka 24 Jam', 5000.00, -7.1606870, 112.7744520);
INSERT INTO `wisata` VALUES (7, 2, 'Pantai Goa Petapa', 'Pantai dengan goa dan spot snorkeling kecil.', 'Labang, Bangkalan', '', 0.00, -7.1576460, 112.8023530);
INSERT INTO `wisata` VALUES (8, 2, 'Pantai Batu Malang', 'Pantai dengan formasi batu besar dan pemandangan alam yang eksotis.', 'Labang, Bangkalan', NULL, NULL, -7.1583280, 112.8057320);

-- ----------------------------
-- Table structure for wisata_gambar
-- ----------------------------
DROP TABLE IF EXISTS `wisata_gambar`;
CREATE TABLE `wisata_gambar`  (
  `id_gambar` int NOT NULL AUTO_INCREMENT,
  `id_wisata` int NOT NULL,
  `file_gambar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_gambar`) USING BTREE,
  INDEX `id_wisata`(`id_wisata` ASC) USING BTREE,
  CONSTRAINT `wisata_gambar_ibfk_1` FOREIGN KEY (`id_wisata`) REFERENCES `wisata` (`id_wisata`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of wisata_gambar
-- ----------------------------
INSERT INTO `wisata_gambar` VALUES (1, 1, 'bukitarosbaya.jpg');
INSERT INTO `wisata_gambar` VALUES (2, 2, 'bukitjaddih.jpeg');
INSERT INTO `wisata_gambar` VALUES (3, 3, 'geger.jpg');
INSERT INTO `wisata_gambar` VALUES (4, 4, 'rongkang.jpg');
INSERT INTO `wisata_gambar` VALUES (5, 5, 'kemuning.jpg');
INSERT INTO `wisata_gambar` VALUES (6, 6, 'rindu.jpg');
INSERT INTO `wisata_gambar` VALUES (7, 7, 'petapa.jpg');
INSERT INTO `wisata_gambar` VALUES (9, 8, '1764508008_batu.jpg');

SET FOREIGN_KEY_CHECKS = 1;
