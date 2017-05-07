using System;
using Penneo;
using System.Collections.Generic;
using System.Linq;
using System.Diagnostics;

namespace Pennneo
{
    public class FilterCaseFiles
    {
        public static void Main(string[] args)
        {
            if (args.Length != 4) {
                Console.WriteLine("Parameters required: endpoint, key, secret, file");
                Environment.Exit(-1);
            }

            string endpoint = args[0];
            string key      = args[1];
            string secret   = args[2];
            string file     = args[3];

            PenneoConnector.Initialize(key, secret, endpoint);
            PenneoConnector.SetLogger(new Logger());
            run(file);
        }

        public static void run(String file)
        {
            // Case file
            //
            var cf = new CaseFile("Sample Case File");
            cf.Persist();

            // Document
            //
            var doc = new Document(cf, "Sample Document", file);
            doc.MakeSignable();
            doc.Persist();

            // Signer
            //
            var signer = new Signer(cf, "John Doe");
            signer.OnBehalfOf = "Acme Corporation";
            // signer.SocialSecurityNumber = "0101501111";
            // signer.VATIdentificationNumber = 12345678;
            signer.Persist();


            // Signature Line
            //
            var sigLine = new SignatureLine(doc, "signer") {
                SignOrder = 0
            };
            sigLine.Persist();

            if (sigLine.Id == null) {
                Console.WriteLine("Unable to create a signature line");
                return;
            }

            // Map the signer to the document using the signature line
            //
            sigLine.SetSigner(signer);

            // Update the signing request
            //
            var signingRequest = signer.GetSigningRequest();
            if (signingRequest.Id == null) {
                Console.WriteLine("Unable to create the signing request");
                return;
            }

            // Optionally enable access control
            //
            // signingRequest.AccessControl = true;
            // signingRequest.Persist();

            // Create the signing request link
            //
            var link = signingRequest.GetLink();

            // Print the link
            //
            Console.WriteLine(link);
        }
    }

    internal class Logger : ILogger
    {
        public void Log(string message, LogSeverity severity)
        {
            Console.WriteLine(severity + ": " + message);
        }
    }
}
