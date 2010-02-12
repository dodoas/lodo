/**
 * DataExchangeSoap.java
 *
 * This file was auto-generated from WSDL
 * by the Apache Axis WSDL2Java emitter.
 */

package no.AltInn.webservices;

public interface DataExchangeSoap extends java.rmi.Remote {
    public java.lang.String submitBatch(java.lang.String batch) throws java.rmi.RemoteException;
    public java.lang.String submitBatchPw(java.lang.String batch, java.lang.String password) throws java.rmi.RemoteException;
    public java.lang.String batchStatus(int enterpriseSystemId, java.lang.String batchId) throws java.rmi.RemoteException;
    public java.lang.String batchStatusPw(int enterpriseSystemId, java.lang.String batchId, java.lang.String password) throws java.rmi.RemoteException;
    public java.lang.String batchReceipt(int enterpriseSystemId, java.lang.String batchId, int version) throws java.rmi.RemoteException;
    public java.lang.String batchReceiptPw(int enterpriseSystemId, java.lang.String batchId, java.lang.String password, int version) throws java.rmi.RemoteException;
    public java.lang.String getSchemaDefinition(long orNumber, long orVersion) throws java.rmi.RemoteException;
    public java.lang.String activeForms() throws java.rmi.RemoteException;
    public java.lang.String altInnStatus() throws java.rmi.RemoteException;
}
