-- 1. 대리점 테이블 (마스터 테이블)
CREATE TABLE expertnote_rent_dealer (
    idx BIGINT AUTO_INCREMENT PRIMARY KEY,
    dealer_code VARCHAR(20) NOT NULL UNIQUE COMMENT '대리점 코드 (영문 대문자)',
    dealer_name VARCHAR(100) NOT NULL COMMENT '대리점명 (한글)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_dealer_code (dealer_code),
    INDEX idx_dealer_name (dealer_name)
) COMMENT '대리점 정보';

-- 2. 차량 기본 정보 테이블 (대리점 종속)
CREATE TABLE expertnote_rent (
    idx BIGINT AUTO_INCREMENT PRIMARY KEY,
    dealer_idx BIGINT NOT NULL COMMENT '대리점 IDX (필수)',
    car_type ENUM ('NEW', 'USED') NOT NULL DEFAULT 'NEW' COMMENT '차량 상태 (신차: NEW, 중고차: USED)',
    car_number VARCHAR(20) NOT NULL COMMENT '차량번호',
    title VARCHAR(100) NOT NULL COMMENT '차량명',
    model_year VARCHAR(10) COMMENT '차량연식 연',
    model_month VARCHAR(10) COMMENT '차량연식 월',
    mileage_km INT COMMENT '주행거리(km)',
    fuel_type VARCHAR(20) COMMENT '연료타입 (휘발유, 경유, 전기 등)',
    option_exterior TEXT COMMENT '옵션(외관 및 내장)',
    option_safety TEXT COMMENT '옵션(안전장치)',
    option_convenience TEXT COMMENT '옵션(편의장치)',  
    option_seat TEXT COMMENT '옵션(시트)',
    contract_terms JSON COMMENT '계약조건',
    driver_range JSON COMMENT '운전자 범위',
    view_count INT DEFAULT 0 COMMENT '조회수',
    wish_count INT DEFAULT 0 COMMENT '찜 갯수',
    original_url VARCHAR(500) COMMENT '원본 페이지 URL',
    `status` ENUM('active', 'rented', 'maintenance', 'deleted') DEFAULT 'active' COMMENT '차량 상태',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    crawled_at TIMESTAMP COMMENT '크롤링 일시',
    
    -- 외래키 및 제약조건
    FOREIGN KEY (dealer_idx) REFERENCES expertnote_rent_dealer(idx) ON DELETE CASCADE,
    
    -- 대리점별 차량번호 유니크 제약조건 (같은 대리점 내에서만 차량번호 중복 불가)
    UNIQUE KEY unique_dealer_car_number (dealer_idx, car_number),
    
    -- 기본 인덱스들
    INDEX idx_dealer_idx (dealer_idx),
    INDEX idx_car_number (car_number),
    INDEX idx_title (title),
    INDEX idx_status (`status`),
    INDEX idx_car_type (car_type),
    INDEX idx_fuel_type (fuel_type),
    INDEX idx_mileage (mileage_km),
    INDEX idx_created_at (created_at),
    INDEX idx_view_count (view_count),
    INDEX idx_wish_count (wish_count),
    
    -- 복합 인덱스들 (성능 최적화)
    INDEX idx_dealer_status (dealer_idx, `status`),
    INDEX idx_dealer_car_type (dealer_idx, car_type),
    INDEX idx_dealer_fuel (dealer_idx, fuel_type),
    INDEX idx_status_car_type (`status`, car_type)
) COMMENT '차량 기본 정보 (대리점 종속)';

-- 3. 차량별 가격 옵션 테이블
CREATE TABLE expertnote_rent_price (
    idx BIGINT AUTO_INCREMENT PRIMARY KEY,
    rent_idx BIGINT NOT NULL COMMENT '차량 IDX',
    deposit_amount INT COMMENT '보증금 (만원)',
    rental_period_months INT COMMENT '렌트 기간 (개월)',
    monthly_rent_amount INT COMMENT '월 렌트비 (원)',
    yearly_mileage_limit INT COMMENT '연간 주행 제한 (만km)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- 외래키
    FOREIGN KEY (rent_idx) REFERENCES expertnote_rent(idx) ON DELETE CASCADE,
    
    -- 인덱스들
    INDEX idx_rent_idx (rent_idx),
    INDEX idx_monthly_rent (monthly_rent_amount),
    INDEX idx_deposit (deposit_amount),
    INDEX idx_period (rental_period_months),
    INDEX idx_mileage_limit (yearly_mileage_limit),
    
    -- 복합 인덱스 (가격 범위 검색용)
    INDEX idx_price_range (monthly_rent_amount, deposit_amount),
    INDEX idx_period_rent (rental_period_months, monthly_rent_amount)
) COMMENT '차량별 가격 옵션';

-- 5. 차량 이미지 테이블
CREATE TABLE expertnote_rent_images (
    idx BIGINT AUTO_INCREMENT PRIMARY KEY,
    rent_idx BIGINT NOT NULL COMMENT '차량 IDX',
    image_url VARCHAR(500) NOT NULL COMMENT '이미지 URL (S3 URL)',
    original_url VARCHAR(500) COMMENT '원본 이미지 URL',
    image_order INT DEFAULT 0 COMMENT '이미지 순서',
    file_size INT COMMENT '파일 크기 (bytes)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- 외래키
    FOREIGN KEY (rent_idx) REFERENCES expertnote_rent(idx) ON DELETE CASCADE,

    -- 인덱스들
    INDEX idx_rent_idx (rent_idx),
    INDEX idx_image_order (rent_idx, image_order),
    INDEX idx_image_url (image_url),
    INDEX idx_original_url (original_url)
) COMMENT '차량 이미지';

-- 6. 보험 조건 테이블 (대리점별)
CREATE TABLE expertnote_rent_insurance (
    idx BIGINT AUTO_INCREMENT PRIMARY KEY,
    dealer_idx BIGINT NOT NULL COMMENT '대리점 IDX (보험 조건은 대리점별로 설정)',
    
    -- 책임한도
    liability_personal VARCHAR(20) COMMENT '대인 책임한도',
    liability_property VARCHAR(20) COMMENT '대물 책임한도',
    liability_self_injury VARCHAR(20) COMMENT '자손 책임한도',
    
    -- 면책금
    deductible_personal VARCHAR(20) COMMENT '대인 면책금',
    deductible_property VARCHAR(20) COMMENT '대물 면책금',
    deductible_self_injury VARCHAR(20) COMMENT '자손 면책금',
    deductible_own_car VARCHAR(20) COMMENT '자차 면책금',
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- 외래키
    FOREIGN KEY (dealer_idx) REFERENCES expertnote_rent_dealer(idx) ON DELETE CASCADE,
    
    -- 인덱스 및 제약조건
    INDEX idx_dealer_idx (dealer_idx),
    UNIQUE KEY unique_dealer_insurance (dealer_idx) -- 대리점당 하나의 보험 조건만
) COMMENT '보험 조건 (대리점별 기본 설정)';