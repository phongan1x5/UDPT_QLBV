-- =====================================================
-- HOSPITAL MANAGEMENT SYSTEM DATABASE SETUP
-- psql -U anphong -h localhost -p 5432 -d postgres -f setup_hospital_db.sql
-- change the username accroding to your system config
-- =====================================================

-- Create databases for each microservice
CREATE DATABASE auth_service;
CREATE DATABASE patient_service;
CREATE DATABASE staff_service;
CREATE DATABASE appointment_service;
CREATE DATABASE medical_record_service;
CREATE DATABASE lab_service;
CREATE DATABASE pharmacy_service;
CREATE DATABASE notification_service;

-- =====================================================
-- AUTH SERVICE DATABASE
-- =====================================================
\c auth_service;

-- Create auth tables
CREATE TABLE IF NOT EXISTS users (
    id VARCHAR PRIMARY KEY,
    hashed_password VARCHAR NOT NULL,
    role VARCHAR NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample auth data
INSERT INTO users (id, hashed_password, role) VALUES
('AD1', '$2b$12$t.u5ca6pUu8YpFeTgxa5SOFHEt97xeWwLyxjz0sGmPBBzbeSWsQ6m', 'admin'),
('DS2', '$2b$12$t.u5ca6pUu8YpFeTgxa5SOFHEt97xeWwLyxjz0sGmPBBzbeSWsQ6m', 'desk_staff'),
('DR3', '$2b$12$t.u5ca6pUu8YpFeTgxa5SOFHEt97xeWwLyxjz0sGmPBBzbeSWsQ6m', 'doctor'),
('DR4', '$2b$12$t.u5ca6pUu8YpFeTgxa5SOFHEt97xeWwLyxjz0sGmPBBzbeSWsQ6m', 'doctor'),
('NS5', '$2b$12$t.u5ca6pUu8YpFeTgxa5SOFHEt97xeWwLyxjz0sGmPBBzbeSWsQ6m', 'nurse'),
('LS6', '$2b$12$t.u5ca6pUu8YpFeTgxa5SOFHEt97xeWwLyxjz0sGmPBBzbeSWsQ6m', 'lab_staff'),
('PH7', '$2b$12$t.u5ca6pUu8YpFeTgxa5SOFHEt97xeWwLyxjz0sGmPBBzbeSWsQ6m', 'pharmacist'),
('BN1', '$2b$12$t.u5ca6pUu8YpFeTgxa5SOFHEt97xeWwLyxjz0sGmPBBzbeSWsQ6m', 'patient'),
('BN2', '$2b$12$t.u5ca6pUu8YpFeTgxa5SOFHEt97xeWwLyxjz0sGmPBBzbeSWsQ6m', 'patient'),
('BN3', '$2b$12$t.u5ca6pUu8YpFeTgxa5SOFHEt97xeWwLyxjz0sGmPBBzbeSWsQ6m', 'patient');

-- =====================================================
-- STAFF SERVICE DATABASE
-- =====================================================
\c staff_service;

-- Create departments table
CREATE TABLE IF NOT EXISTS "PhongBan" (
    "IDPhongBan" SERIAL PRIMARY KEY,
    "TenPhongBan" VARCHAR NOT NULL,
    "IDTruongPhongBan" INTEGER
);

-- Create staff table
CREATE TABLE IF NOT EXISTS "NhanVien" (
    "MaNhanVien" SERIAL PRIMARY KEY,
    "IDPhongBan" INTEGER,
    "HoTen" VARCHAR NOT NULL,
    "NgaySinh" VARCHAR NOT NULL,
    "DiaChi" VARCHAR NOT NULL,
    "Email" VARCHAR NOT NULL,
    "SoDienThoai" VARCHAR NOT NULL,
    "SoDinhDanh" VARCHAR NOT NULL UNIQUE,
    "LoaiNhanVien" VARCHAR NOT NULL,
    "ChuyenKhoa" VARCHAR
);

-- Sample department data
INSERT INTO "PhongBan" ("TenPhongBan", "IDTruongPhongBan") VALUES
('Administration', NULL),
('Cardiology', 3),
('Emergency', 4),
('Laboratory', 6),
('Pharmacy', 7),
('Nursing', 5),
('Pediatrics', NULL),
('Surgery', NULL),
('Radiology', NULL),
('Internal Medicine', NULL);

-- Sample staff data
INSERT INTO "NhanVien" ("IDPhongBan", "HoTen", "NgaySinh", "DiaChi", "Email", "SoDienThoai", "SoDinhDanh", "LoaiNhanVien", "ChuyenKhoa") VALUES
(1, 'Global Admin', '1985-01-15', '123 Admin Street, Ho Chi Minh City', 'admin@hospital.com', '0901234567', '123456789001', 'admin', NULL),
(1, 'Receptionist Nguyen', '1990-03-20', '456 Reception Ave, Ho Chi Minh City', 'reception@hospital.com', '0901234568', '123456789002', 'desk_staff', NULL),
(2, 'Dr. Tran Van A', '1980-05-10', '789 Doctor St, Ho Chi Minh City', 'doctor1@hospital.com', '0901234569', '123456789003', 'doctor', 'Cardiology'),
(3, 'Dr. Le Thi B', '1982-07-25', '321 Medical Rd, Ho Chi Minh City', 'doctor2@hospital.com', '0901234570', '123456789004', 'doctor', 'Emergency Medicine'),
(6, 'desk_staff Pham C', '1988-11-12', '654 Desking Blvd, Ho Chi Minh City', 'desk_staff2@hospital.com', '0901234571', '123456789005', 'desk_staff', NULL),
(4, 'Lab Tech Hoang D', '1985-09-30', '987 Lab Lane, Ho Chi Minh City', 'lab1@hospital.com', '0901234572', '123456789006', 'lab_staff', NULL),
(5, 'Pharmacist Vu E', '1983-12-05', '147 Pharmacy Plaza, Ho Chi Minh City', 'pharmacy1@hospital.com', '0901234573', '123456789007', 'pharmacist', NULL),
(2, 'Dr. Nguyen F', '1978-04-18', '258 Heart Ave, Ho Chi Minh City', 'cardio@hospital.com', '0901234574', '123456789008', 'doctor', 'Cardiology'),
(6, 'Nurse Tran G', '1992-06-22', '369 Care Street, Ho Chi Minh City', 'lab_staff3@hospital.com', '0901234575', '123456789009', 'lab_staff', NULL),
(4, 'Lab Supervisor H', '1981-08-14', '741 Test Drive, Ho Chi Minh City', 'labsup@hospital.com', '0901234576', '123456789010', 'lab_staff', NULL);

-- Update department heads
UPDATE "PhongBan" SET "IDTruongPhongBan" = 3 WHERE "IDPhongBan" = 2;
UPDATE "PhongBan" SET "IDTruongPhongBan" = 4 WHERE "IDPhongBan" = 3;
UPDATE "PhongBan" SET "IDTruongPhongBan" = 6 WHERE "IDPhongBan" = 4;
UPDATE "PhongBan" SET "IDTruongPhongBan" = 7 WHERE "IDPhongBan" = 5;
UPDATE "PhongBan" SET "IDTruongPhongBan" = 5 WHERE "IDPhongBan" = 6;

-- =====================================================
-- PATIENT SERVICE DATABASE
-- =====================================================
\c patient_service;

-- Create patients table
CREATE TABLE IF NOT EXISTS patients (
    id SERIAL PRIMARY KEY,
    "HoTen" VARCHAR NOT NULL,
    "NgaySinh" DATE NOT NULL,
    "GioiTinh" VARCHAR NOT NULL,
    "SoDienThoai" VARCHAR NOT NULL,
    "SoDinhDanh" VARCHAR NOT NULL UNIQUE,
    "Email" VARCHAR NOT NULL,
    "DiaChi" VARCHAR,
    "BaoHiemYTe" VARCHAR,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample patient data
INSERT INTO patients ("HoTen", "NgaySinh", "GioiTinh", "SoDienThoai", "SoDinhDanh", "Email", "DiaChi", "BaoHiemYTe", is_active) VALUES
('Nguyen Van A', '1990-01-15', 'Nam', '0912345678', '123456789101', 'patient1@email.com', '123 Patient St, District 1, HCMC', 'BH123456789', true),
('Tran Thi B', '1985-05-20', 'Nữ', '0912345679', '123456789102', 'patient2@email.com', '456 Health Ave, District 2, HCMC', 'BH123456790', true),
('Le Van C', '1992-03-10', 'Nam', '0912345680', '123456789103', 'patient3@email.com', '789 Care Blvd, District 3, HCMC', 'BH123456791', true),
('Pham Thi D', '1988-07-25', 'Nữ', '0912345681', '123456789104', 'patient4@email.com', '321 Wellness Rd, District 4, HCMC', 'BH123456792', true),
('Hoang Van E', '1995-11-12', 'Nam', '0912345682', '123456789105', 'patient5@email.com', '654 Medical Lane, District 5, HCMC', 'BH123456793', true),
('Vu Thi F', '1987-09-30', 'Nữ', '0912345683', '123456789106', 'patient6@email.com', '987 Hospital Dr, District 6, HCMC', 'BH123456794', true),
('Dang Van G', '1993-12-05', 'Nam', '0912345684', '123456789107', 'patient7@email.com', '147 Treatment Ave, District 7, HCMC', 'BH123456795', true),
('Bui Thi H', '1989-04-18', 'Nữ', '0912345685', '123456789108', 'patient8@email.com', '258 Recovery St, District 8, HCMC', 'BH123456796', true),
('Do Van I', '1991-06-22', 'Nam', '0912345686', '123456789109', 'patient9@email.com', '369 Healing Blvd, District 9, HCMC', 'BH123456797', true),
('Ngo Thi J', '1986-08-14', 'Nữ', '0912345687', '123456789110', 'patient10@email.com', '741 Clinic Circle, District 10, HCMC', 'BH123456798', true);

-- =====================================================
-- MEDICAL RECORD SERVICE DATABASE
-- =====================================================
\c medical_record_service;

-- Create medical profiles table (HoSoBenhAn)
CREATE TABLE IF NOT EXISTS "HoSoBenhAn" (
    "MaHSBA" SERIAL PRIMARY KEY,
    "MaBenhNhan" INTEGER NOT NULL,
    "TienSu" TEXT,
    "is_active" BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create medical records table (GiayKhamBenh)
CREATE TABLE IF NOT EXISTS "GiayKhamBenh" (
    "MaGiayKhamBenh" SERIAL PRIMARY KEY,
    "MaHSBA" INTEGER ,
    "BacSi" INTEGER NOT NULL,
    "MaLichHen" INTEGER,
    "NgayKham" DATE NOT NULL,
    "ChanDoan" TEXT NOT NULL,
    "LuuY" TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample medical profile data
INSERT INTO "HoSoBenhAn" ("MaBenhNhan", "TienSu") VALUES
(1, 'No significant medical history. Patient reports occasional headaches.'),
(2, 'History of hypertension, controlled with medication. No known allergies.'),
(3, 'Previous appendectomy in 2020. No current medications.'),
(4, 'Diabetes Type 2, managed with diet and exercise. Regular check-ups required.'),
(5, 'No chronic conditions. Patient is physically active.'),
(6, 'History of asthma, uses inhaler as needed. No recent episodes.'),
(7, 'Previous fracture in left arm, fully healed. No complications.'),
(8, 'High cholesterol, taking statins. Family history of heart disease.'),
(9, 'No significant medical history. Occasional seasonal allergies.'),
(10, 'Migraine sufferer, managed with prescription medication.');

-- Sample medical record data
INSERT INTO "GiayKhamBenh" ("MaHSBA", "BacSi", "MaLichHen", "NgayKham", "ChanDoan", "LuuY") VALUES
(1, 3, 1, '2024-01-15', 'Routine check-up, patient in good health', 'Continue current lifestyle, schedule follow-up in 6 months'),
(2, 3, 2, '2024-01-16', 'Hypertension monitoring, blood pressure stable', 'Continue medication, reduce salt intake'),
(3, 4, 3, '2024-01-17', 'Post-surgical follow-up, healing well', 'No restrictions, normal activity resumed'),
(4, 3, 4, '2024-01-18', 'Diabetes management, HbA1c within target range', 'Continue current diet plan, increase exercise'),
(5, 4, 5, '2024-01-19', 'Sports physical examination, cleared for activities', 'No restrictions, maintain fitness routine'),
(6, 3, 6, '2024-01-20', 'Asthma follow-up, symptoms well controlled', 'Continue current inhaler, avoid triggers'),
(7, 4, 7, '2024-01-21', 'Fracture follow-up, bone healing complete', 'Full range of motion restored, no limitations'),
(8, 3, 8, '2024-01-22', 'Cardiac risk assessment, cholesterol improved', 'Continue statin therapy, heart-healthy diet'),
(9, 4, 9, '2024-01-23', 'Annual physical, all parameters normal', 'Continue healthy lifestyle, next visit in 1 year'),
(10, 3, 10, '2024-01-24', 'Migraine management, frequency reduced', 'Continue current medication, stress management');

-- =====================================================
-- APPOINTMENT SERVICE DATABASE
-- =====================================================
\c appointment_service;

-- Create appointments table
CREATE TABLE IF NOT EXISTS "LichHen" (
    "MaLichHen" SERIAL PRIMARY KEY,
    "MaBenhNhan" INTEGER NOT NULL,
    "MaBacSi" INTEGER NOT NULL,
    "Ngay" VARCHAR NOT NULL,
    "Gio" VARCHAR NOT NULL,
    "LoaiLichHen" VARCHAR NOT NULL,
    "TrangThai" VARCHAR NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample appointment data
INSERT INTO "LichHen" ("MaBenhNhan", "MaBacSi", "Ngay", "Gio", "LoaiLichHen", "TrangThai") VALUES
(1, 3, '2024-01-15', '09:00:00', 'KhamMoi', 'DaKham'),
(2, 3, '2024-01-16', '09:30:00', 'KhamMoi', 'DaKham'),
(3, 4, '2024-01-17', '10:00:00', 'TaiKham', 'DaKham'),
(4, 3, '2024-01-18', '10:30:00', 'KhamMoi', 'DaKham'),
(5, 4, '2024-01-19', '11:00:00', 'KhamMoi', 'DaKham'),
(6, 3, '2024-01-20', '11:30:00', 'TaiKham', 'DaKham'),
(7, 4, '2024-01-21', '14:00:00', 'KhamMoi', 'DaKham'),
(8, 3, '2024-01-22', '14:30:00', 'KhamMoi', 'DaKham'),
(9, 4, '2024-01-23', '15:00:00', 'KhamMoi', 'DaKham'),
(10, 3, '2024-01-24', '15:30:00', 'TaiKham', 'DaKham');

-- =====================================================
-- LAB SERVICE DATABASE
-- =====================================================
\c lab_service;

-- Create lab services table (DichVu)
CREATE TABLE IF NOT EXISTS "DichVu" (
    "MaDichVu" SERIAL PRIMARY KEY,
    "TenDichVu" VARCHAR NOT NULL,
    "NoiDungDichVu" VARCHAR NOT NULL,
    "DonGia" DECIMAL(10,2) NOT NULL
);

-- Create used services table (DichVuSuDung)
CREATE TABLE IF NOT EXISTS "DichVuSuDung" (
    "MaDVSD" SERIAL PRIMARY KEY,
    "MaDichVu" INTEGER REFERENCES "DichVu"("MaDichVu"),
    "MaGiayKhamBenh" INTEGER NOT NULL,
    "YeuCauCuThe" VARCHAR,
    "ThoiGian" VARCHAR NOT NULL,
    "KetQua" VARCHAR,
    "FileKetQua" VARCHAR,
    "TrangThai" VARCHAR NOT NULL
);

-- Sample lab services
INSERT INTO "DichVu" ("TenDichVu", "NoiDungDichVu", "DonGia") VALUES
('Complete Blood Count', 'Full blood analysis including RBC, WBC, platelets, hemoglobin', 150000.00),
('Blood Sugar Test', 'Fasting and random glucose level measurement', 80000.00),
('Lipid Profile', 'Cholesterol and triglyceride levels assessment', 200000.00),
('Liver Function Test', 'ALT, AST, bilirubin levels to assess liver health', 250000.00),
('Kidney Function Test', 'Creatinine, BUN levels to assess kidney function', 180000.00),
('Thyroid Function Test', 'TSH, T3, T4 levels to assess thyroid function', 300000.00),
('Urine Analysis', 'Complete urine examination for infections and abnormalities', 100000.00),
('Chest X-Ray', 'Digital chest radiography for lung and heart examination', 350000.00),
('ECG', 'Electrocardiogram to assess heart rhythm and function', 120000.00),
('Ultrasound Abdomen', 'Abdominal ultrasound for organ examination', 400000.00);

-- Sample used services
INSERT INTO "DichVuSuDung" ("MaDichVu", "MaGiayKhamBenh", "YeuCauCuThe", "ThoiGian", "KetQua", "FileKetQua", "TrangThai") VALUES
(1, 1, 'Routine health screening', '2024-01-15 10:30:00', 'Normal values, no abnormalities detected', NULL, 'DaCoKetQua'),
(2, 2, 'Monitor diabetes control', '2024-01-16 11:00:00', 'Glucose: 110 mg/dL - Within normal range', NULL, 'DaCoKetQua'),
(3, 3, 'Cardiovascular risk assessment', '2024-01-17 09:45:00', 'Total Cholesterol: 185 mg/dL - Acceptable', NULL, 'DaCoKetQua'),
(4, 4, 'Liver health check', '2024-01-18 14:15:00', 'ALT: 25 U/L, AST: 28 U/L - Normal', NULL, 'DaCoKetQua'),
(5, 5, 'Pre-operative assessment', '2024-01-19 08:30:00', 'Creatinine: 0.9 mg/dL - Normal kidney function', NULL, 'DaCoKetQua'),
(6, 6, 'Thyroid disorder screening', '2024-01-20 13:00:00', 'TSH: 2.5 mIU/L - Normal thyroid function', NULL, 'DaCoKetQua'),
(7, 7, 'UTI symptoms evaluation', '2024-01-21 16:45:00', 'No bacteria or blood cells detected', NULL, 'DaCoKetQua'),
(8, 8, 'Respiratory symptoms check', '2024-01-22 12:30:00', 'Clear lung fields, normal heart size', 'chest_xray_008.jpg', 'DaCoKetQua'),
(9, 9, 'Heart palpitations investigation', '2024-01-23 11:15:00', 'Normal sinus rhythm, no arrhythmias', 'ecg_009.pdf', 'DaCoKetQua'),
(10, 10, 'Abdominal pain evaluation', '2024-01-24 15:20:00', 'Normal liver, kidney, gallbladder appearance', 'ultrasound_010.jpg', 'DaCoKetQua');

-- =====================================================
-- PHARMACY SERVICE DATABASE
-- =====================================================
\c pharmacy_service;

-- Create medicines table (Thuoc)
CREATE TABLE IF NOT EXISTS "Thuoc" (
    "MaThuoc" SERIAL PRIMARY KEY,
    "TenThuoc" VARCHAR NOT NULL,
    "DonViTinh" VARCHAR NOT NULL,
    "ChiDinh" VARCHAR,
    "SoLuongTonKho" INTEGER NOT NULL,
    "GiaTien" DECIMAL(10,2) NOT NULL
);

-- Create prescriptions table (ToaThuoc)
CREATE TABLE IF NOT EXISTS "ToaThuoc" (
    "MaToaThuoc" SERIAL PRIMARY KEY,
    "MaGiayKhamBenh" INTEGER NOT NULL,
    "TrangThaiToaThuoc" VARCHAR NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create prescription medicines table (ThuocTheoToa)
CREATE TABLE IF NOT EXISTS "ThuocTheoToa" (
    id SERIAL PRIMARY KEY,
    "MaToaThuoc" INTEGER REFERENCES "ToaThuoc"("MaToaThuoc"),
    "MaThuoc" INTEGER REFERENCES "Thuoc"("MaThuoc"),
    "SoLuong" INTEGER NOT NULL,
    "GhiChu" VARCHAR
);

-- Sample medicines
INSERT INTO "Thuoc" ("TenThuoc", "DonViTinh", "ChiDinh", "SoLuongTonKho", "GiaTien") VALUES
('Paracetamol 500mg', 'Tablet', 'Pain relief, fever reduction', 1000, 2000.00),
('Amoxicillin 250mg', 'Capsule', 'Bacterial infections', 500, 15000.00),
('Ibuprofen 400mg', 'Tablet', 'Anti-inflammatory, pain relief', 800, 3500.00),
('Omeprazole 20mg', 'Capsule', 'Gastric acid reduction', 300, 8000.00),
('Metformin 500mg', 'Tablet', 'Type 2 diabetes management', 600, 4500.00),
('Lisinopril 10mg', 'Tablet', 'Hypertension treatment', 400, 12000.00),
('Aspirin 100mg', 'Tablet', 'Cardioprotection, pain relief', 1200, 1500.00),
('Simvastatin 20mg', 'Tablet', 'Cholesterol management', 350, 18000.00),
('Cough Syrup', 'Bottle 120ml', 'Cough suppression', 150, 25000.00),
('Vitamin D3 1000IU', 'Capsule', 'Bone health, immune support', 500, 6000.00);

-- Sample prescriptions
INSERT INTO "ToaThuoc" ("MaGiayKhamBenh", "TrangThaiToaThuoc") VALUES
(1, 'Active'),
(2, 'Active'),
(3, 'Active'),
(4, 'Active'),
(5, 'Active'),
(6, 'Active'),
(7, 'Active'),
(8, 'Active'),
(9, 'Active'),
(10, 'Active');

-- Sample prescription medicines
INSERT INTO "ThuocTheoToa" ("MaToaThuoc", "MaThuoc", "SoLuong", "GhiChu") VALUES
(1, 1, 20, 'Take 1-2 tablets every 6 hours as needed for pain'),
(1, 10, 30, 'Take 1 capsule daily with breakfast'),
(2, 6, 30, 'Take 1 tablet daily in the morning'),
(2, 7, 90, 'Take 1 tablet daily for cardioprotection'),
(3, 3, 15, 'Take 1 tablet twice daily with food'),
(4, 5, 60, 'Take 1 tablet twice daily before meals'),
(4, 4, 30, 'Take 1 capsule daily before breakfast'),
(5, 1, 10, 'Take as needed for pain, maximum 8 tablets per day'),
(6, 2, 21, 'Take 1 capsule three times daily for 7 days'),
(7, 3, 20, 'Take 1 tablet twice daily with food for 10 days'),
(8, 8, 30, 'Take 1 tablet daily in the evening'),
(8, 7, 90, 'Take 1 tablet daily for cardioprotection'),
(9, 9, 1, 'Take 10ml three times daily for cough'),
(10, 1, 15, 'Take 1-2 tablets as needed for headache');

-- =====================================================
-- NOTIFICATION SERVICE DATABASE
-- =====================================================
\c notification_service;

-- Create notifications table
CREATE TABLE IF NOT EXISTS notifications (
    id SERIAL PRIMARY KEY,
    "userId" VARCHAR NOT NULL,
    "sourceSystem" VARCHAR NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample notifications
INSERT INTO notifications ("userId", "sourceSystem", message) VALUES
('BN1', 'Laboratory', 'Your blood test results are now available. Please check your patient portal.'),
('BN2', 'Pharmacy', 'Your prescription is ready for pickup at the hospital pharmacy.'),
('BN3', 'Appointment', 'Reminder: You have an appointment tomorrow at 10:00 AM with Dr. Tran.'),
('AD1', 'System', 'Weekly system maintenance completed successfully.'),
('DR3', 'Laboratory', 'New lab results available for patient Nguyen Van A.'),
('BN4', 'Laboratory', 'Your diabetes monitoring results show good glucose control.'),
('NS5', 'ADMIN', 'Please attend the monthly staff meeting on Friday at 2 PM.'),
('BN5', 'Appointment', 'Your appointment has been confirmed for next Monday at 11:00 AM.'),
('PH7', 'Pharmacy', 'Low stock alert: Amoxicillin inventory below minimum threshold.'),
('BN6', 'Medical Record', 'Your medical record has been updated with recent test results.');

-- =====================================================
-- CREATE INDEXES FOR BETTER PERFORMANCE
-- =====================================================

-- Auth service indexes
\c auth_service;
CREATE INDEX idx_users_role ON users(role);

-- Patient service indexes
\c patient_service;
CREATE INDEX idx_patients_phone ON patients("SoDienThoai");
CREATE INDEX idx_patients_email ON patients("Email");
CREATE INDEX idx_patients_active ON patients(is_active);

-- Staff service indexes
\c staff_service;
CREATE INDEX idx_staff_department ON staff("IDPhongBan");
CREATE INDEX idx_staff_type ON staff("LoaiNhanVien");
CREATE INDEX idx_staff_email ON staff("Email");

-- Appointment service indexes
\c appointment_service;
CREATE INDEX idx_appointments_patient ON appointments("MaBenhNhan");
CREATE INDEX idx_appointments_doctor ON appointments("MaBacSi");
CREATE INDEX idx_appointments_date ON appointments("Ngay");
CREATE INDEX idx_appointments_status ON appointments("TrangThai");

-- Medical record service indexes
\c medical_record_service;
CREATE INDEX idx_medical_records_patient ON "HoSoBenhAn"("MaBenhNhan");
CREATE INDEX idx_medical_records_doctor ON "GiayKhamBenh"("BacSi");
CREATE INDEX idx_medical_records_date ON "GiayKhamBenh"("NgayKham");

-- Lab service indexes
\c lab_service;
CREATE INDEX idx_used_services_record ON "DichVuSuDung"("MaGiayKhamBenh");
CREATE INDEX idx_used_services_status ON "DichVuSuDung"("TrangThai");

-- Pharmacy service indexes
\c pharmacy_service;
CREATE INDEX idx_prescriptions_record ON "ToaThuoc"("MaGiayKhamBenh");
CREATE INDEX idx_prescriptions_status ON "ToaThuoc"("TrangThaiToaThuoc");

-- Notification service indexes
\c notification_service;
CREATE INDEX idx_notifications_user ON notifications("userId");
CREATE INDEX idx_notifications_source ON notifications("sourceSystem");
CREATE INDEX idx_notifications_created ON notifications(created_at);

-- =====================================================
-- SUMMARY
-- =====================================================

\c auth_service;
SELECT 'Auth Service - Users: ' || COUNT(*) FROM users;

\c patient_service;
SELECT 'Patient Service - Patients: ' || COUNT(*) FROM patients;

\c staff_service;
SELECT 'Staff Service - Staff: ' || COUNT(*) FROM staff;
SELECT 'Staff Service - Departments: ' || COUNT(*) FROM departments;

\c appointment_service;
SELECT 'Appointment Service - Appointments: ' || COUNT(*) FROM appointments;

\c medical_record_service;
SELECT 'Medical Record Service - Profiles: ' || COUNT(*) FROM "HoSoBenhAn";
SELECT 'Medical Record Service - Records: ' || COUNT(*) FROM "GiayKhamBenh";

\c lab_service;
SELECT 'Lab Service - Services: ' || COUNT(*) FROM "DichVu";
SELECT 'Lab Service - Used Services: ' || COUNT(*) FROM "DichVuSuDung";

\c pharmacy_service;
SELECT 'Pharmacy Service - Medicines: ' || COUNT(*) FROM "Thuoc";
SELECT 'Pharmacy Service - Prescriptions: ' || COUNT(*) FROM "ToaThuoc";

\c notification_service;
SELECT 'Notification Service - Notifications: ' || COUNT(*) FROM notifications;

-- =====================================================
-- END OF SCRIPT
-- =====================================================