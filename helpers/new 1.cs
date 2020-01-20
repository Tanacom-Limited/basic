
public static bool ValidateHeader()
{
	// return true;
	//byte[] nn = System.Text.ASCIIEncoding.ASCII.GetBytes("Ah the Admin User is here");

	//ds = addToDs(Convert.ToBase64String(nn), "thisTable");

	string origin = HttpContext.Current.Request.Headers["Origin"];
	object rfr = HttpContext.Current.Request.ServerVariables["HTTP_REFERER"];
	//ds = addToDs(ASCIIEncoding.ASCII.GetString(ss), "thisTable");
	//Util.WriteToSecurityLog("hostname: " + rfr.ToString());
	string isValidUser = "InvalidUser";
	string str = HttpContext.Current.Request.Headers["AsaaseUser"];
	if (!string.IsNullOrEmpty(str))
	{
		byte[] ss = Convert.FromBase64String(str);
		str = ASCIIEncoding.ASCII.GetString(ss);
		DataTable dt = new DataTable();
		//HttpContext.Current.Cache.Remove("AsaaseUser");
		//if (HttpContext.Current.Session["AsaaseUser"] != null)
		//{
	   //     dt = (DataTable)HttpContext.Current.Session["AsaaseUser"];
		//}
		//else
		//{
			SqlDataAdapter da = new SqlDataAdapter("SELECT CatchPhrase,IPAddress, TheOwner FROM gp_User", ConfigurationManager.ConnectionStrings["asaaseAPIUser"].ConnectionString);
			da.Fill(dt);
		 //   HttpContext.Current.Session["AsaaseUser"] = dt;
		//}
		DataRow[] drs = dt.Select("CatchPhrase='" + str + "'");
		//Util.WriteToApplicationLog("DRS Length: " + drs.Length.ToString() + "|CatchPhrase:" + str );
		if (drs.Length > 0)
		{

			localisValidUser = drs[0]["TheOwner"].ToString();

			if (localisValidUser == "Website")
			{
				if (rfr != null)
				{
					Uri _origin = new Uri(origin);
					Uri _referrer = new Uri(rfr.ToString());
					string _hostURL = ConfigurationManager.AppSettings["hostURL"];
					if (_origin.Authority.EndsWith(_hostURL) && _referrer.Authority.EndsWith(_hostURL))
						isValidUser = localisValidUser;
					/*
					//Util.WriteToSecurityLog("hostname refer: " + rfr.ToString());
					if (origin.StartsWith("https://asaasegps.com") || origin.StartsWith("https://www.asaasegps.com"))
						if (rfr.ToString().StartsWith("https://asaasegps.com") || rfr.ToString().StartsWith("https://www.asaasegps.com"))
						isValidUser = localisValidUser;
					*/
				}
			}
			else if (localisValidUser.StartsWith("android", StringComparison.InvariantCultureIgnoreCase))
			{
				if(HttpContext.Current.Request.Headers["X-Android-Cert"] == ConfigurationManager.AppSettings["androidcert"]
					&& HttpContext.Current.Request.Headers["X-Android-Package"] == ConfigurationManager.AppSettings["androidname"])
				{
					localisValidUser = "Android";
					isValidUser = localisValidUser;
				}
			}
			else
				if (drs[0]["IPAddress"].ToString() != "*")
				//if (localisValidUser == "SMS")
				{
					string myIp = GetIPAddress; string allowedIPs = drs[0]["IPAddress"].ToString();
					//Util.WriteToApplicationLog("IP/Allows: " + myIp + "|" + allowedIPs + "|" + localisValidUser);
					if (drs[0]["IPAddress"].ToString().Contains(GetIPAddress))// == "34.213.231.255")
					{
						isValidUser = localisValidUser;
					}
				}
				else
					isValidUser = localisValidUser;

		}
	}
	//thisUser = isValidUser;
	bool userValidated = isValidUser != "InvalidUser";
	if (!userValidated)
		LogActivity(isValidUser, getUserActivity());
	return userValidated;
}
    
public static bool ValidateHeader()
{
	bool allow = false;
	try{
		string Origin = context.Request.Headers["Origin"].ToString();
		//string Referer = context.Request.Headers["Referer"].ToString();
		
		List<string> siteAddr = new List<string>(new string[] { "https://www.mtnhoods.com", "https://mtnhoods.com", "mtnhoods.com", "http://mtnhoods.com" });
		
		if(siteAddr.Contains(Origin)){
			allow = true;
		}
	}
	catch(Exception e){
		
	}
	return allow;
}