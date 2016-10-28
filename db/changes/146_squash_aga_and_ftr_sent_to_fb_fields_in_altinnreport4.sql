ALTER TABLE altinnReport4
ADD SentToFakturabankAt datetime DEFAULT NULL,
ADD SentToFakturabankBy int(11) DEFAULT NULL;

UPDATE altinnReport4
SET
SentToFakturabankAt = CASE WHEN SentAGAToFakturabankAt > SentFTRToFakturabankAt THEN SentAGAToFakturabankAt ELSE SentFTRToFakturabankAt END,
SentToFakturabankBy = CASE WHEN SentAGAToFakturabankAt > SentFTRToFakturabankAt THEN SentAGAToFakturabankBy ELSE SentFTRToFakturabankBy END;

ALTER TABLE altinnReport4
DROP SentAGAToFakturabankAt,
DROP SentAGAToFakturabankBy,
DROP SentFTRToFakturabankAt,
DROP SentFTRToFakturabankBy;