-- 1. 대리점 테이블 (마스터 테이블)
CREATE TABLE expertnote_rent_dealer (
    idx BIGINT AUTO_INCREMENT PRIMARY KEY,
    dealer_code VARCHAR(20) NOT NULL UNIQUE COMMENT '대리점 코드 (영문 대문자)',
    dealer_name VARCHAR(100) NOT NULL COMMENT '대리점명 (한글)',
    driver_range JSON COMMENT '운전자 범위 (대리점별 공통)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_dealer_code (dealer_code),
    INDEX idx_dealer_name (dealer_name)
) COMMENT '대리점 정보';

-- 기존 데이터 마이그레이션 (필요시 실행)
-- ALTER TABLE expertnote_rent_dealer ADD COLUMN driver_range JSON COMMENT '운전자 범위 (대리점별 공통)' AFTER dealer_name;

-- 2. 차량 기본 정보 테이블 (대리점 종속)
CREATE TABLE expertnote_rent (
    idx BIGINT AUTO_INCREMENT PRIMARY KEY,
    dealer_idx BIGINT NOT NULL COMMENT '대리점 IDX (필수)',
    car_type ENUM ('NEW', 'USED') NOT NULL DEFAULT 'NEW' COMMENT '차량 상태 (신차: NEW, 중고차: USED)',
    car_number VARCHAR(20) NOT NULL COMMENT '차량번호',
    brand_idx INT COMMENT '브랜드 IDX (rent_brand 참조)',
    model_idx INT COMMENT '모델 IDX (rent_model 참조)',
    grade VARCHAR(100) COMMENT '차량 등급 (트림)',
    color VARCHAR(50) COMMENT '차량 색상',
    title VARCHAR(100) NOT NULL COMMENT '차량명 (표시용)',
    image VARCHAR(500) COMMENT '대표 이미지 URL',
    monthly_price INT COMMENT '월 렌트료 (원)',
    model_year VARCHAR(10) COMMENT '차량연식 연',
    model_month VARCHAR(10) COMMENT '차량연식 월',
    mileage_km INT COMMENT '주행거리(km)',
    fuel_type VARCHAR(20) COMMENT '연료타입 (휘발유, 경유, 전기 등)',
    option_exterior TEXT COMMENT '옵션(외관 및 내장)',
    option_safety TEXT COMMENT '옵션(안전장치)',
    option_convenience TEXT COMMENT '옵션(편의장치)',
    option_seat TEXT COMMENT '옵션(시트)',
    option_main TEXT COMMENT '옵션(주요장치)',
    option_etc TEXT COMMENT '옵션(기타)',
    contract_terms JSON COMMENT '계약조건',
    -- driver_range는 대리점(expertnote_rent_dealer)으로 이동됨
    view_count INT DEFAULT 0 COMMENT '조회수',
    wish_count INT DEFAULT 0 COMMENT '찜 갯수',
    original_url VARCHAR(500) COMMENT '원본 페이지 URL',
    `status` ENUM('active', 'rented', 'maintenance', 'deleted') DEFAULT 'active' COMMENT '차량 상태',
    is_sticky ENUM('Y', 'N') DEFAULT 'N' COMMENT '상단 고정 여부',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    crawled_at TIMESTAMP COMMENT '크롤링 일시',

    -- 외래키 및 제약조건
    FOREIGN KEY (dealer_idx) REFERENCES expertnote_rent_dealer(idx) ON DELETE CASCADE,
    FOREIGN KEY (brand_idx) REFERENCES expertnote_rent_brand(idx) ON DELETE SET NULL,
    FOREIGN KEY (model_idx) REFERENCES expertnote_rent_model(idx) ON DELETE SET NULL,

    -- 대리점별 차량번호 유니크 제약조건 (같은 대리점 내에서만 차량번호 중복 불가)
    UNIQUE KEY unique_dealer_car_number (dealer_idx, car_number),

    -- 기본 인덱스들
    INDEX idx_dealer_idx (dealer_idx),
    INDEX idx_car_number (car_number),
    INDEX idx_brand_idx (brand_idx),
    INDEX idx_model_idx (model_idx),
    INDEX idx_title (title),
    INDEX idx_status (`status`),
    INDEX idx_car_type (car_type),
    INDEX idx_fuel_type (fuel_type),
    INDEX idx_mileage (mileage_km),
    INDEX idx_created_at (created_at),
    INDEX idx_view_count (view_count),
    INDEX idx_wish_count (wish_count),
    INDEX idx_monthly_price (monthly_price),

    -- 복합 인덱스들 (성능 최적화)
    INDEX idx_dealer_status (dealer_idx, `status`),
    INDEX idx_dealer_car_type (dealer_idx, car_type),
    INDEX idx_dealer_fuel (dealer_idx, fuel_type),
    INDEX idx_status_car_type (`status`, car_type),
    INDEX idx_brand_model (brand_idx, model_idx),

    -- FULLTEXT 인덱스 (연관 차량 검색용)
    FULLTEXT INDEX ft_title (title)
) ENGINE=InnoDB COMMENT '차량 기본 정보 (대리점 종속)';

-- 3. 차량별 가격 옵션 테이블
CREATE TABLE expertnote_rent_price (
    idx BIGINT AUTO_INCREMENT PRIMARY KEY,
    rent_idx BIGINT NOT NULL COMMENT '차량 IDX',
    deposit_amount INT COMMENT '보증금 (만원)',
    rental_period_months INT COMMENT '렌트 기간 (개월)',
    monthly_rent_amount INT COMMENT '월 렌트비 (원)',
    yearly_mileage_limit INT COMMENT '연간 주행 제한 (만km)',
    contract_type ENUM('선택형', '반납형') DEFAULT '선택형' COMMENT '만기 후 방법 (선택형: 인수/반납 선택, 반납형: 반납만 가능)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- 외래키
    FOREIGN KEY (rent_idx) REFERENCES expertnote_rent(idx) ON DELETE CASCADE,

    -- 인덱스들
    INDEX idx_rent_idx (rent_idx),
    INDEX idx_monthly_rent (monthly_rent_amount),
    INDEX idx_deposit (deposit_amount),
    INDEX idx_period (rental_period_months),
    INDEX idx_mileage_limit (yearly_mileage_limit),
    INDEX idx_contract_type (contract_type),

    -- 복합 인덱스 (가격 범위 검색용)
    INDEX idx_price_range (monthly_rent_amount, deposit_amount),
    INDEX idx_period_rent (rental_period_months, monthly_rent_amount)
) COMMENT '차량별 가격 옵션';

-- 기존 테이블에 contract_type 컬럼 추가 (마이그레이션용)
-- ALTER TABLE expertnote_rent_price ADD COLUMN contract_type ENUM('선택형', '반납형') DEFAULT '선택형' COMMENT '만기 후 방법 (선택형: 인수/반납 선택, 반납형: 반납만 가능)' AFTER yearly_mileage_limit;

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

    -- 운전자 조건
    min_driver_age INT COMMENT '최저 운전자 연령',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- 외래키
    FOREIGN KEY (dealer_idx) REFERENCES expertnote_rent_dealer(idx) ON DELETE CASCADE,
    
    -- 인덱스 및 제약조건
    INDEX idx_dealer_idx (dealer_idx),
    UNIQUE KEY unique_dealer_insurance (dealer_idx) -- 대리점당 하나의 보험 조건만
) COMMENT '보험 조건 (대리점별 기본 설정)';

-- 7. 차량 찜하기(위시리스트) 테이블
CREATE TABLE expertnote_rent_wishlist (
    idx INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '찜하기 고유 ID',
    user_id VARCHAR(30) DEFAULT NULL COMMENT '사용자 아이디 (로그인 시)',
    ip_address VARCHAR(45) NOT NULL COMMENT 'IP 주소 (IPv4/IPv6)',
    rent_idx BIGINT NOT NULL COMMENT '차량 IDX',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '찜한 시간',

    PRIMARY KEY (idx),

    -- 외래키
    FOREIGN KEY (rent_idx) REFERENCES expertnote_rent(idx) ON DELETE CASCADE,

    -- 유니크 제약조건 (같은 IP에서 같은 차량 중복 찜 방지)
    UNIQUE KEY unique_ip_rent (ip_address, rent_idx),

    -- 인덱스들
    INDEX idx_user_id (user_id),
    INDEX idx_ip_address (ip_address),
    INDEX idx_rent_idx (rent_idx),
    INDEX idx_created_at (created_at)
) COMMENT '차량 찜하기 테이블 (IP 기반, 로그인 선택)';

-- 8. 브랜드 테이블
CREATE TABLE expertnote_rent_brand (
    idx INT AUTO_INCREMENT PRIMARY KEY,
    brand_name VARCHAR(50) NOT NULL COMMENT '브랜드명',
    brand_name_en VARCHAR(50) COMMENT '브랜드명 (영문)',
    country_code CHAR(2) NOT NULL COMMENT '국가 코드 (KR, DE, US, JP 등)',
    logo_url VARCHAR(500) COMMENT '브랜드 로고 URL',
    sort_order INT DEFAULT 0 COMMENT '정렬 순서',
    is_active TINYINT(1) DEFAULT 1 COMMENT '활성화 여부',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY unique_brand_name (brand_name),
    INDEX idx_country_code (country_code),
    INDEX idx_sort_order (sort_order),
    INDEX idx_is_active (is_active)
) COMMENT '차량 브랜드';

-- 9. 모델 테이블
CREATE TABLE expertnote_rent_model (
    idx INT AUTO_INCREMENT PRIMARY KEY,
    brand_idx INT NOT NULL COMMENT '브랜드 IDX',
    model_name VARCHAR(100) NOT NULL COMMENT '모델명',
    model_name_en VARCHAR(100) COMMENT '모델명 (영문)',
    segment VARCHAR(20) COMMENT '차급 (경차, 소형, 준중형, 중형, 대형, SUV 등)',
    sort_order INT DEFAULT 0 COMMENT '정렬 순서',
    is_active TINYINT(1) DEFAULT 1 COMMENT '활성화 여부',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (brand_idx) REFERENCES expertnote_rent_brand(idx) ON DELETE CASCADE,
    UNIQUE KEY unique_brand_model (brand_idx, model_name),
    INDEX idx_brand_idx (brand_idx),
    INDEX idx_model_name (model_name),
    INDEX idx_segment (segment),
    INDEX idx_sort_order (sort_order),
    INDEX idx_is_active (is_active)
) COMMENT '차량 모델';

-- ALTER: expertnote_rent에 is_sticky 컬럼 추가
-- ALTER TABLE expertnote_rent ADD COLUMN is_sticky ENUM('Y', 'N') DEFAULT 'N' COMMENT '상단 고정 여부' AFTER `status`;