package br.com.sampaio;

import java.awt.image.BufferedImage;
import java.io.File;
import java.util.HashMap;
import java.util.Map;

import javax.imageio.ImageIO;

import com.google.zxing.BinaryBitmap;
import com.google.zxing.EncodeHintType;
import com.google.zxing.RGBLuminanceSource;
import com.google.zxing.Result;
import com.google.zxing.common.HybridBinarizer;
import com.google.zxing.multi.qrcode.QRCodeMultiReader;
import com.google.zxing.qrcode.decoder.ErrorCorrectionLevel;

public class Main {

	public static void main(String[] args) {
		try {
			File file = new File(args[0]);
			
	        BufferedImage image = ImageIO.read(file);
	        int[] pixels = image.getRGB(0, 0, image.getWidth(), image.getHeight(), null, 0, image.getWidth());
	        RGBLuminanceSource source = new RGBLuminanceSource(image.getWidth(), image.getHeight(), pixels);
	        BinaryBitmap bitmap = new BinaryBitmap(new HybridBinarizer(source));
	
			Map hintMap = new HashMap<EncodeHintType, ErrorCorrectionLevel>();
		    hintMap.put(EncodeHintType.ERROR_CORRECTION, ErrorCorrectionLevel.L);
		    
			Result[] qrCodeResult = new QRCodeMultiReader().decodeMultiple(bitmap, hintMap);
			
			for (Result result2 : qrCodeResult) {
				System.out.println(result2.getText().trim());
			}
		}
		catch (Exception e)
		{
			//ignore console output
		}
		
	}
}
