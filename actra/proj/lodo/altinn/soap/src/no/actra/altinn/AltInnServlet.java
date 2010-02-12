/*
 * Created on 05.apr.2005
 *
 * TODO To change the template for this generated file go to
 * Window - Preferences - Java - Code Style - Code Templates
 */

package no.actra.altinn;
import java.io.*;
import java.net.URL;
import java.util.*;

import javax.servlet.*;
import javax.servlet.http.*;


import org.apache.axis.AxisFault;
import org.apache.axis.client.Service;

import no.AltInn.webservices.DataExchange;
import no.AltInn.webservices.DataExchangeLocator;
import no.AltInn.webservices.DataExchangeSoap;
import no.AltInn.webservices.DataExchangeSoapStub;

/**
 * @author Håkon
 *
 * TODO To change the template for this generated type comment go to
 * Window - Preferences - Java - Code Style - Code Templates
 */
public class AltInnServlet extends HttpServlet {
	private String logFileName = "altinnEx.log";
	private String faultFileName = "altinnFault.log";
	private String certFileName  = "mycacerts";

	public void doGet(HttpServletRequest request, HttpServletResponse response)
    throws IOException, ServletException
    {
        PrintWriter out = response.getWriter ();
		try {
//	    	String path = System.getProperty("user.dir")+"/../webapps/altinn/";
	    	String path = System.getProperty("user.dir")+"/webapps/altinn/";
			System.setProperty("javax.net.ssl.trustStore", path + certFileName);
			System.setProperty("javax.net.ssl.trustStorePassword","changeit");

			
			DataExchange locator = new DataExchangeLocator();
			URL url = new URL(locator.getDataExchangeSoapAddress());
//			URL url = new URL("https://www.altinn.no/webservices/dataexchange.asmx");
			Service service = new Service(locator.getServiceName());
			DataExchangeSoap altinn = new DataExchangeSoapStub(url,service);

			String retur = "";
			retur = altinn.altInnStatus();
			out.println("\nAltinn status ("+ url.toString()+"):\n"+retur+"\n\n");

			if (new File(path+logFileName).exists()){
				FileReader logFile = new FileReader(path+logFileName);
				BufferedReader buffer = new BufferedReader(logFile);
				
				out.println("Utskrift fra exceptionlogfil:\n");
				String linje;
				while((linje = buffer.readLine()) != null){
						out.println(linje);
				}
			}else{
				out.println("Logfil finnes ikke ennå.\n");
			}
			if (new File(path+faultFileName).exists()){
				FileReader logFile = new FileReader(path+faultFileName);
				BufferedReader buffer = new BufferedReader(logFile);
				
				out.println("Utskrift fra feilstatuslog:\n");
				String linje;
				while((linje = buffer.readLine()) != null){
						out.println(linje);
				}
			}else{
				out.println("Altinn feilstatuslog finnes ikke ennå.\n");
			}
//			url = new URL("https://www.altinn.no/webservices/dataexchange.asmx");
//			service = new Service(locator.getServiceName());
//			altinn = new DataExchangeSoapStub(url,service);
//			retur = altinn.altInnStatus();
//			out.println("\n\nAltinn status ("+ url.toString()+"):\n "+retur);
			
			
		} catch (AxisFault fault) {
			out.println("Fault: "+fault);
		} catch (Exception ex) {
			out.println("Exception e: "+ex);
		}
 }

    public void doPost(HttpServletRequest request, HttpServletResponse res)
    throws IOException, ServletException
    {
		FileOutputStream logFile = null;
		FileOutputStream faultFile = null;
		PrintStream pLog = null;
		PrintStream pFault = null;
//    	String path = System.getProperty("user.dir")+"/../webapps/altinn/";
    	String path = System.getProperty("user.dir")+"/webapps/altinn/";

    	System.setProperty("javax.net.ssl.trustStore", path + certFileName);
//		System.setProperty("javax.net.ssl.trustStore", System.getProperty("user.home")+"/webapps/altinn/mycacerts");
		System.setProperty("javax.net.ssl.trustStorePassword","changeit");
		
		Calendar cal = Calendar.getInstance(TimeZone.getDefault());
	    String DATE_FORMAT = "yyyy-MM-dd HH:mm:ss";
	    java.text.SimpleDateFormat sdf = new java.text.SimpleDateFormat(DATE_FORMAT);
//	    sdf.setTimeZone(TimeZone.getDefault());          

	    PrintWriter out = res.getWriter ();
        String passord = request.getParameter("passord");
        
        String melding = createMessage(request);
        
		try {
			logFile = new FileOutputStream(path + logFileName,true);
			faultFile = new FileOutputStream(path + faultFileName,true);
			pLog = new PrintStream(logFile);
			pFault = new PrintStream(faultFile);

			String retur = "";
			DataExchange locator = new DataExchangeLocator();
			URL url = new URL(locator.getDataExchangeSoapAddress());
			Service service = new Service(locator.getServiceName());
			DataExchangeSoap altinn = new DataExchangeSoapStub(url,service);

//			retur = altinn.altInnStatus();
//			out.println("\nAltinn status:\n"+retur);
			
			retur = altinn.submitBatchPw(melding,passord);

			String status = getAltinnStatus(retur);
			out.println("Status: "+status);

	    	out.println("\nMelding sendt:\n"+melding+"\n\nRetur fra Altinn:\n"+retur);
	    	if (!status.startsWith("OK")&&(pFault != null)){
	    		pFault.println(sdf.format(cal.getTime())+" Status: "+status.replace('\n','\t'));
	    	}

		} catch (AxisFault fault) {
		    System.out.println("Now : " + sdf.format(cal.getTime()));

			out.println("Fault: "+fault);
			if (pLog != null){
				pLog.println(sdf.format(cal.getTime())+" Axisfault: "+ fault);
			}
		} catch (Exception ex) {
			out.println("Exception e: "+ex);
			if (pLog != null){
				pLog.println(sdf.format(cal.getTime())+" Exception: "+ ex);
			}
		}
		pLog.close();
		logFile.close();
    }
    
    /**
     * Takes the input parameter and generates the XML message that is sent to Altinn
     * 
     * @param request		Values that is to be inserted in the message
     * @return				The complete XML message
     */
    private String createMessage(HttpServletRequest request){
    	String skjema      = createSkjema("212","3148",request.getParameter("data"));
    	String dataUnit    = createDataUnit(request.getParameter("participantId"),request.getParameter("sendersReference"),"true",skjema);
    	String dataUnit2    = createDataUnit(request.getParameter("participantId"),"321","true",skjema);
    	String attachment  = createAttachment(request.getParameter("enterpriseSystemId"),request.getParameter("sendersReference"),request.getParameter("parentReference"),request.getParameter("fileName"));
    	String dataUnits   = createDataUnits(new String[] {dataUnit});
//    	String dataUnits   = createDataUnits(new String[] {dataUnit,dataUnit2}); // kun for test av to units
    	String attachments = createAttachments(new String[] {attachment});
    	String melding     = createDataBatch(request.getParameter("batchId"),request.getParameter("enterpriseSystemId"),dataUnits,"");
    	return melding;
    }
    
    /**
     * Creates the skjema element that is to be included in a data unit 
     * @param skjemanr
     * @param spesifikasjonsnr
     * @param innhold
     * @return 				XML formatted skjema element
     */
    private String createSkjema(String skjemanr, String spesifikasjonsnr, String innhold){
    	String skjema = "<Skjema ";
        skjema += "xmlns:brreg=\"http://www.brreg.no/or\" ";
        skjema += "xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" ";
        skjema += "skjemanummer=\"" + skjemanr +"\" ";
        skjema += "spesifikasjonsnummer=\"" + spesifikasjonsnr +"\" ";
        skjema += "blankettnummer=\"RF-0002\" ";
        skjema += "tittel=\"Alminnelig omsetningsoppgave\" ";
        skjema += "gruppeid=\"20\" ";
        skjema += "etatid=\"974761076\">";
        skjema += innhold;
        skjema += "</Skjema>";
   	return skjema;
    }

    /**
     * Creates one data unit to insert in the AltInn message.
     * 
     * @param participantId	Organization number or birth number to identify the sender of the data
     * @param reference		
     * @param completed		Value that determines if the message is complete or not
     * @param skjema		The skjema element to be inserted in the data unit
     * @return				XML formatted data unit element
     */
    private String createDataUnit(String participantId, String reference, 
    						   String completed, String skjema){
    	String dataUnit = "<DataUnit ";
        dataUnit += "participantId=\"" + participantId + "\" ";
        dataUnit += "sendersReference=\"" + reference +"\" "; 
        dataUnit += "completed=\"" + completed + "\">";
        dataUnit += skjema;
        dataUnit += "</DataUnit>";
   	return dataUnit;
    }
    
    /**
     * Merges the data unit elements that are to be inserted in a message to AltInn
     * 
     * @param dataUnit		All data units that are to be merged together
     * @return 				XML formatted data units element
     */
    private String createDataUnits(String[] dataUnit){
    	String units = "<DataUnits>";
    	for (int i = 0; i < dataUnit.length; i++){
    		units += dataUnit[i];
    	}
    	units += "</DataUnits>";
    	return units;
    }
    
    /**
     * Creates one attachment to insert in the Altinn message
     * 
     * @param fagsystemId	A number to identify the sender and what system he uses to send data to AltInn
     * @param sendersRef	
     * @param parentRef
     * @param filnavn		File name of the attachment
     * @return 				XML formatted attachment element
     */
    private String createAttachment(String fagsystemId, String sendersRef, 
    							 String parentRef, String filnavn){
    	String attachment = "<Attachment ";
    	attachment += "participantId=\""+ fagsystemId +"\" ";
    	attachment += "sendersReference=\"" + sendersRef + "\" ";
    	attachment += "parentReference=\"" + parentRef + "\" ";
    	attachment += "fileName=\"" + filnavn + "\"/>";
    	return attachment;
    }
    
    /**
     * Merges the attatchment elements that are to be inserted in a message to AltInn 
     * 
     * @param attachment	An array of attachments that are to be sent to AltInn
     * @return 				XML formatted attahcments element
     */
    private String createAttachments(String[] attachment){
    	String attachments = "<Attachments>";
    	for (int i = 0; i < attachment.length; i++ ){
    		attachments += attachment[i];
    	}
    	attachments += "</Attachments>";
    	return attachments;
    }

    /**
     * createDataBatch
     * 
     * This function merges the data units and attachments into a complete data batch for sending to AtlInn
     * @param batch
     * @param fagsystemId	Identification number to identify the sender and data system used 
     * @param units
     * @param attachments
     * @return 				XML formatted data batch
     */
    private String createDataBatch(String batch, String fagsystemId, String units, String attachments){
    	String dataBatch = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
    	dataBatch += "<DataBatch schemaVersion=\"1.1\" ";
    	dataBatch += "batchId=\"" + batch +"\" ";
    	dataBatch += "enterpriseSystemId=\"" + fagsystemId + "\" "; 
    	dataBatch += "receiptType=\"OnDemand\">";
    	dataBatch += units;
    	dataBatch += attachments;
    	dataBatch += "</DataBatch>";
    	return dataBatch;
    }

    /**
     * Extracts status message from the return value received from AltInn
     * 
     * @param returnValue	AltInn return value
     * @return				Status on the batch sending
     */
    private String getAltinnStatus(String returnValue){
    	String batchReceipt = "DataBatchInReceipt";
    	String unitReceipt  = "DataUnitInReceipt";
    	String sendersRef   = "sendersReference";
    	String status       = "status";
    	String end          = "\"";
    	String newline      = "\n";
    	String result       = "";
    	
		int startBatch = returnValue.indexOf(batchReceipt);
		int endBatch   = returnValue.indexOf("/"+batchReceipt) + batchReceipt.length() + 1;
		int start = returnValue.indexOf(status,startBatch)+status.length()+2;
		result = returnValue.substring(start,returnValue.indexOf(end,start)) + newline;
		result += batchReceipt + "=" + result;
		
		int startUnit = returnValue.indexOf(unitReceipt);
		int endUnit   = returnValue.indexOf("/"+unitReceipt) + unitReceipt.length() + 1;
		int i = 0;
		while ((startUnit != -1)&&(startUnit < endUnit)) {
			start = returnValue.indexOf(sendersRef,start) + sendersRef.length() + 2;
			result += unitReceipt + " " + sendersRef + "=" + returnValue.substring(start,returnValue.indexOf(end,start));
			start = returnValue.indexOf(status,start) + status.length() + 2;
			result += " " + status + "=" + returnValue.substring(start,returnValue.indexOf(end,start));
			startUnit = returnValue.indexOf(unitReceipt,endUnit);
			endUnit   = returnValue.indexOf("/"+unitReceipt,endUnit)+unitReceipt.length()+1;
			result +=  newline;
		};

		return result;
    }
}
