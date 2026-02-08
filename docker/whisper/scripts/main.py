import os
import time
import logging
import tempfile
import torch
import whisper

from fastapi import FastAPI, UploadFile, File
from fastapi.responses import JSONResponse

# ---------- логирование ----------
logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s | %(levelname)s | %(message)s",
)
logger = logging.getLogger("whisper-api")

# ---------- конфиг ----------
MODEL_NAME = os.getenv("WHISPER_MODEL", "small")
DEVICE = "cuda" if torch.cuda.is_available() else "cpu"

# ---------- FastAPI ----------
app = FastAPI(title="Whisper API")

model = None


@app.on_event("startup")
def load_model():
    global model

    logger.info("🚀 Whisper API starting")
    logger.info(f"📦 Model: {MODEL_NAME}")
    logger.info(f"🖥 Device: {DEVICE}")

    start = time.time()

    logger.info("⏳ Loading Whisper model...")
    model = whisper.load_model(MODEL_NAME, device=DEVICE)

    elapsed = time.time() - start
    logger.info(f"✅ Model loaded in {elapsed:.2f}s")
    logger.info("🎧 Whisper API ready")


@app.post("/transcribe")
async def transcribe(file: UploadFile = File(...)):
    logger.info(f"📥 Incoming file: {file.filename}")

    with tempfile.NamedTemporaryFile(delete=False, suffix=file.filename) as tmp:
        tmp.write(await file.read())
        tmp_path = tmp.name

    try:
        logger.info("🧠 Transcribing...")
        result = model.transcribe(tmp_path)

        logger.info("✅ Transcription done")

        return JSONResponse({
            "text": result["text"],
            "language": result["language"],
            "segments": result["segments"],
        })
    finally:
        os.remove(tmp_path)
        logger.info("🧹 Temp file removed")
