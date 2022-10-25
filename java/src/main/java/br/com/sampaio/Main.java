package br.com.sampaio;

import java.awt.image.BufferedImage;
import java.io.File;
import java.io.PrintWriter;
import java.io.StringWriter;
import java.util.HashMap;
import java.util.Map;

import javax.imageio.ImageIO;

import com.google.zxing.BarcodeFormat;
import com.google.zxing.BinaryBitmap;
import com.google.zxing.DecodeHintType;
import com.google.zxing.RGBLuminanceSource;
import com.google.zxing.Result;
import com.google.zxing.common.GlobalHistogramBinarizer;
import com.google.zxing.datamatrix.DataMatrixReader;

public class Main {

	public static void main(String[] args) {
		try {
			File file = new File("/home/lrs/git/upwork/pdf_qrcode_reader/test/descentralizado.png");
			
	        BufferedImage image = ImageIO.read(file);
	        int[] pixels = image.getRGB(0, 0, image.getWidth(), image.getHeight(), null, 0, image.getWidth());
	        RGBLuminanceSource source = new RGBLuminanceSource(image.getWidth(), image.getHeight(), pixels);
	        BinaryBitmap bitmap = new BinaryBitmap(new GlobalHistogramBinarizer(source));
	
			Map<DecodeHintType, Object> hints = new HashMap<DecodeHintType, Object>();
			hints.put(DecodeHintType.TRY_HARDER, Boolean.TRUE);
			hints.put(DecodeHintType.POSSIBLE_FORMATS, BarcodeFormat.DATA_MATRIX);

			DataMatrixReader reader = new DataMatrixReader();

			Result qrCodeResult = reader.decode(bitmap, hints);
			
			System.out.println(qrCodeResult.getText().trim());
		}
		catch (Exception e)
		{
			e.printStackTrace();
		}
	}
}
