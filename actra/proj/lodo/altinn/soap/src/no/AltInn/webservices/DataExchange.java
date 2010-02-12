/**
 * DataExchange.java
 *
 * This file was auto-generated from WSDL
 * by the Apache Axis WSDL2Java emitter.
 */

package no.AltInn.webservices;

public interface DataExchange extends javax.xml.rpc.Service {
    public java.lang.String getDataExchangeSoapAddress();

    public no.AltInn.webservices.DataExchangeSoap getDataExchangeSoap() throws javax.xml.rpc.ServiceException;

    public no.AltInn.webservices.DataExchangeSoap getDataExchangeSoap(java.net.URL portAddress) throws javax.xml.rpc.ServiceException;
}
